# Skill 09: Hệ thống Thông báo Đa kênh (Multi-channel Notification System)

Hệ thống này được thiết kế để xử lý việc gửi thông báo đồng thời qua nhiều kênh (Database, Email, FCM) một cách hiệu quả, bảo mật và không làm giảm hiệu năng ứng dụng.

## 1. Tư duy kiến trúc (Architectural Mindset)

### 1.1. Xử lý bất đồng bộ (Queue-First)
Thông báo thường được gửi cho số lượng lớn người dùng. Nếu gửi đồng bộ (Synchronous), ứng dụng sẽ bị treo (Timeout). 
- **Giải pháp:** Luôn sử dụng `ShouldQueue` trong Class Notification.
- **Queue Connection:** Ưu tiên dùng `redis` hoặc `database`.

### 1.2. Audience Service (Tách biệt logic truy vấn)
Đừng bao giờ viết logic lấy danh sách User ngay trong Controller. 
- **Lý do:** Logic chọn đối tượng (ví dụ: "Tất cả học viên đã thi trượt môn Toán trong 7 ngày qua") sẽ rất phức tạp và cần được tái sử dụng ở nhiều nơi.

### 1.3. Đa kênh (Multi-channel)
Laravel Notification hỗ trợ sẵn các channel. Chúng ta chỉ cần map dữ liệu tương ứng:
- **Database:** Dành cho thông báo in-app (hiện ở quả chuông trên header).
- **Mail:** Dành cho nội dung chi tiết, cần lưu trữ lâu dài.
- **FCM (Firebase):** Dành cho thông báo đẩy (Push Notification) lên trình duyệt hoặc app mobile.

---

## 2. Quy trình triển khai Code chuẩn Senior

### Bước 1: Khởi tạo Database
```bash
php artisan notifications:table
php artisan make:migration create_user_fcm_tokens_table
```

### Bước 2: Xây dựng Notification Class trung tâm
Tạo một lớp thông báo tổng quát:
`app/Notifications/GeneralNotification.php`
- Chứa logic định dạng dữ liệu cho từng kênh.
- Sử dụng template email đồng bộ với brand của EduQuiz.

### Bước 3: Audience Service Layer
`app/Services/NotificationAudienceService.php`
```php
public function getAudience($type, $targetId = null) {
    return match($type) {
        'all' => User::all(),
        'students' => User::role('student')->get(),
        'course' => Course::find($targetId)->students,
        default => collect([]),
    };
}
```

### Bước 4: Admin Controller & View
- Xây dựng giao diện gửi thông báo trực quan trong Admin.
- Sử dụng `Notification::send()` để tối ưu hiệu năng gửi hàng loạt.

---

## 3. Lưu ý về Bảo mật & Hiệu năng
1. **Rate Limiting:** Tránh gửi quá nhiều email trong thời gian ngắn (Gmail có giới hạn 500 mail/ngày).
2. **Token Cleanup:** Tự động xóa các FCM Token đã hết hạn khi nhận được lỗi từ Firebase API.
3. **Encryption:** Nếu thông báo chứa thông tin nhạy cảm (như mật khẩu tạm thời), hãy đảm bảo kênh Email được cấu hình SMTP bảo mật (SSL/TLS).

---
*Senior Developer Note: "Build once, notify everywhere."*
