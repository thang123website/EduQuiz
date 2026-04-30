# Kế hoạch & Kiến trúc Kiểm thử (Testing Strategy)
**Mục tiêu:** Đảm bảo hệ thống EduQuiz hoạt động ổn định, dễ bảo trì và mở rộng thông qua quy trình kiểm thử tự động (Automated Testing) chuẩn mực theo mô hình Kim tự tháp Kiểm thử (Test Pyramid).

---

## 1. Kiến trúc Kiểm thử (Testing Pyramid)

Hệ thống EduQuiz sử dụng framework **PHPUnit** (mặc định của Laravel) kết hợp với các kỹ thuật Mocking mạnh mẽ để xây dựng bộ test.

### A. Unit Tests (Kiểm thử Mức Đơn vị)
- **Phạm vi:** Kiểm tra các thành phần độc lập (Service, Model, Helper, Enum).
- **Trọng tâm:** Các logic nghiệp vụ phức tạp (ví dụ: Thuật toán sắp xếp slide, logic sinh mã tự động, logic tính điểm trắc nghiệm sau này).
- **Nguyên tắc:** 
  - Không kết nối trực tiếp với Cơ sở dữ liệu thật (Sử dụng Mock DB hoặc SQLite in-memory).
  - Tốc độ thực thi cực nhanh (chạy hàng nghìn test trong vài giây).

### B. Feature Tests (Kiểm thử Mức Tích hợp/Tính năng)
- **Phạm vi:** Kiểm tra từ đầu đến cuối một chức năng (Request -> Middleware -> Controller -> Service -> Database -> Response).
- **Trọng tâm:** 
  - Xác thực quyền truy cập (Authorization/Gate).
  - Validation dữ liệu đầu vào.
  - Phản hồi của API (JSON format) và thay đổi trạng thái Database.
- **Nguyên tắc:** Dùng Database dành riêng cho Testing (`RefreshDatabase`), giả lập (Mocking) các dịch vụ bên ngoài (như S3, Gửi Email).

---

## 2. Tiêu chuẩn Mã Kiểm thử (Senior Pro Standards)

Để bộ test dễ đọc và dễ bảo trì, chúng ta áp dụng mẫu **AAA (Arrange - Act - Assert)**:

1. **Arrange (Chuẩn bị):** Khởi tạo dữ liệu giả (Factories), thiết lập Mocking, đăng nhập User.
2. **Act (Hành động):** Gọi hàm hoặc gửi HTTP Request đến Endpoints cần test.
3. **Assert (Xác nhận):** So sánh kết quả thực tế với kết quả mong đợi.

### Các công cụ bổ trợ (Best Practices):
- **Mocking Storage:** Luôn sử dụng `Storage::fake('public')` khi test chức năng upload file để tránh rác ổ cứng.
- **Mocking Time:** Dùng `$this->travelTo(now())` khi cần test các mốc thời gian (vd: bài thi hết hạn).
- **Event/Job Faking:** `Event::fake()` và `Queue::fake()` để không làm chậm test bởi các tác vụ chạy ngầm.

---

## 3. Quy trình Triển khai cho Module Slider & Media

### Phase 1: Slider Module Tests
1. **Unit Test - `SliderServiceTest`**:
   - Khởi tạo (Create) Slider và Item mới.
   - Cập nhật thứ tự Drag & Drop (kiểm tra logic đánh số `order`).
   - Xóa Cache tự động khi dữ liệu thay đổi.
2. **Feature Test - `SliderControllerTest`**:
   - Phân quyền: User không có quyền `slider.update` sẽ bị HTTP 403.
   - Validation: Không nhập tên Slider bị lỗi HTTP 422.
   - Luồng lưu/xóa: Gửi POST Request lên API, sau đó kiểm tra Database xem bản ghi đã tồn tại/bị xóa chưa.

### Phase 2: Media Manager Tests
1. **Unit Test - `MediaServiceTest`**:
   - Logic sinh tên file độc nhất để tránh ghi đè.
   - Tạo thumbnail cho ảnh đúng kích thước.
2. **Feature Test - `MediaControllerTest`**:
   - Gửi Request đính kèm file (Sử dụng `UploadedFile::fake()->image('test.jpg')`).
   - Kiểm tra xem file đã được lưu vào Storage giả chưa.
   - Ngăn chặn upload file `.exe`, `.php` (Bảo mật).

---

## 4. Lệnh thực thi (CLI)

- **Chạy toàn bộ test:** `php artisan test`
- **Chạy song song (Parallel) để tăng tốc:** `php artisan test --parallel`
- **Chỉ chạy Feature Test:** `php artisan test --testsuite=Feature`
