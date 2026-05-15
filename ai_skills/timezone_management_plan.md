# Kế hoạch triển khai Hệ thống Quản lý Múi giờ (Timezone Management)

Để xử lý triệt để bài toán "Thời gian hiển thị sai múi giờ" và cho phép cấu hình động múi giờ cho mọi tài khoản trên EduQuiz, chúng ta có thể triển khai theo **2 cấp độ** dưới đây. Tùy thuộc vào quy mô dự án, bạn có thể chọn triển khai Cấp độ 1 hoặc cả hai.

## 1. Cấp độ 1: Cấu hình Múi giờ Toàn cục (Global Timezone)
Áp dụng một múi giờ chung cho toàn bộ hệ thống (ví dụ: `Asia/Ho_Chi_Minh` - GMT+7).

### Bước 1.1: Thiết lập cấu hình hệ thống
- Thêm key `system_timezone` vào trang **Cài đặt hệ thống (Admin Settings)** để Admin có thể tự do chọn múi giờ từ một Dropdown list (các timezone chuẩn của PHP).
- *Tùy chọn nhanh:* Đổi biến `APP_TIMEZONE=Asia/Ho_Chi_Minh` trong file `.env` và chạy `php artisan config:clear`. (Cách này nhanh nhưng phải sửa code/env, không sửa trên giao diện Admin được).

### Bước 1.2: Tạo Timezone Middleware
- Tạo một Middleware (VD: `App\Http\Middleware\TimezoneMiddleware`).
- Trong Middleware này, đọc giá trị timezone từ Settings:
  ```php
  $timezone = \App\Models\Setting::get('system_timezone', config('app.timezone'));
  config(['app.timezone' => $timezone]);
  date_default_timezone_set($timezone);
  ```
- Gắn Middleware này vào nhóm `web` và `api` trong `bootstrap/app.php` (với Laravel 11) hoặc `Kernel.php` để mọi request đều dùng đúng múi giờ này.

---

## 2. Cấp độ 2: Múi giờ cá nhân hóa cho từng User (User-specific Timezone)
Hệ thống cho phép mỗi học viên/giảng viên tự chọn múi giờ riêng biệt. Rất hữu ích nếu EduQuiz phục vụ học viên quốc tế.

### Bước 2.1: Cập nhật CSDL (Database)
- Tạo Migration thêm cột `timezone` (string, nullable) vào bảng `users`.
- Mặc định cột này sẽ lấy theo `system_timezone` (ở Cấp độ 1) nếu user chưa cài đặt.

### Bước 2.2: Cập nhật giao diện Profile
- Bổ sung trường chọn **"Múi giờ (Timezone)"** vào trang Chỉnh sửa hồ sơ cá nhân (Profile) của User.
- Dữ liệu sẽ gọi từ hàm `timezone_identifiers_list()` của PHP để lấy danh sách múi giờ chuẩn quốc tế.

### Bước 2.3: Tự động chuyển đổi hiển thị (Carbon Conversion)
- Cập nhật lại `TimezoneMiddleware` để ưu tiên múi giờ của user:
  ```php
  if (auth()->check() && auth()->user()->timezone) {
      $timezone = auth()->user()->timezone;
      config(['app.timezone' => $timezone]);
      date_default_timezone_set($timezone);
  }
  ```
- **Lưu ý quan trọng:** CSDL (MySQL) vẫn luôn lưu thời gian ở chuẩn chung `UTC` (để tránh lệch giờ khi máy chủ đổi vị trí). Nhưng khi truy vấn ra và hiển thị bằng Carbon, hệ thống sẽ tự dịch múi giờ `UTC` sang múi giờ của người dùng.
  ```php
  // Code mẫu hiển thị an toàn
  $submission->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s')
  ```

---

## Các công việc tiếp theo (Action Items)
Nếu bạn đồng ý với kế hoạch này, tôi sẽ bắt tay vào code các hạng mục sau:
1. Tạo migration `add_timezone_to_users_table`.
2. Viết `TimezoneMiddleware` và inject vào App.
3. Tạo API và sửa giao diện UI Admin để quản lý Timezone chung.
4. (Tùy chọn) Viết Helper/Trait để format toàn bộ `created_at` tự động map với timezone hiện tại mà không cần sửa từng View một.
