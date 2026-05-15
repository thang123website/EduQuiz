# Kế hoạch triển khai Hệ thống Quản lý Form (Form Management System)

## 1. Tổng quan Kiến trúc (Architecture Overview)
- **Mục tiêu**: Xây dựng module nhận, lưu trữ và quản lý các form yêu cầu (liên hệ, đăng ký giảng viên, hỗ trợ, v.v.) từ người dùng (học viên/giảng viên), đồng thời tự động gửi email thông báo cho Admin qua giao thức SMTP đã cấu hình.
- **Tư duy hệ thống**: Thay vì mỗi loại form tạo một bảng riêng (rất khó mở rộng), ta sẽ sử dụng kiến trúc lưu trữ động bằng **JSON format**. Bất kỳ form nào ở frontend (dù thêm bớt trường) đều có thể submit về một API duy nhất, giúp hệ thống cực kỳ linh hoạt và dễ dàng scale trong tương lai mà không cần đụng chạm vào database.

## 2. Thiết kế Cơ sở dữ liệu (Database Design)
Tạo một bảng duy nhất `form_submissions` (hoặc `contact_forms`) để hứng mọi loại form:
- `id`: Khóa chính (BigInt).
- `type`: Loại form (String). Ví dụ: `contact`, `instructor_register`, `support`, `feedback`. Dùng để phân loại và lọc trên Admin.
- `user_id`: Khóa ngoại (Nullable). Nếu người dùng đã đăng nhập (học viên/giảng viên) thì lưu lại ID để dễ tra cứu lịch sử yêu cầu của họ.
- `data`: Kiểu `JSON`. Nơi chứa phép màu của hệ thống, lưu toàn bộ các trường form người dùng submit dưới dạng Key-Value (Ví dụ: `{"name": "Nguyễn Văn A", "email": "a@gmail.com", "reason": "Xin làm giảng viên", "cv_link": "..."}`).
- `status`: Kiểu `string` hoặc `enum` (Ví dụ: `pending` (Chờ xử lý), `processing` (Đang giải quyết), `resolved` (Đã xong), `ignored` (Bỏ qua)). Mặc định là `pending`.
- `ip_address`: Địa chỉ IP của người gửi (Hỗ trợ chống spam).
- `created_at`, `updated_at`.

## 3. Thiết kế API & Backend Logic
- **Endpoint**: `POST /api/v1/forms/{type}`
- **Validation Layer**: 
  - Validate cơ bản các trường dùng chung (email nếu có, recaptcha token).
  - Tùy thuộc vào `{type}`, có thể tạo các `FormRequest` cụ thể để bắt buộc validate các trường JSON nhất định (Ví dụ type = `contact` thì bắt buộc `data.email` và `data.message`).
- **Service Layer**: `FormSubmissionService` chịu trách nhiệm:
  1. Validate và làm sạch dữ liệu đầu vào.
  2. Lưu vào DB bảng `form_submissions`.
  3. Bắn (Trigger) Event `FormSubmittedEvent`.

## 4. Xử lý Sự kiện & Email SMTP (Event Driven)
- Để không làm chậm trải nghiệm của người dùng khi submit form, thao tác gửi mail cần được đẩy vào **Queue/Job** chạy ngầm.
- Khi `FormSubmittedEvent` được gọi, một Listener sẽ đón lấy và khởi tạo Job gửi Email.
- **Mailable (`FormAdminNotificationMail`)**: 
  - Bắt buộc kế thừa layout Email đã có sẵn của hệ thống (`@extends('emails.layout')`) để giữ nguyên giao diện chuẩn, header, footer và logo thương hiệu.
  - Phần nội dung (content) của template sẽ lặp (foreach) qua mảng JSON `data` và in ra dạng danh sách (Tên trường: Giá trị) một cách tự động -> Không cần sửa code email khi frontend thêm trường mới.
- **Người nhận (Admin)**: Email nhận thông báo có thể lấy từ bảng `settings` (Ví dụ: cấu hình email nhận thông báo trong hệ thống) hoặc mặc định gửi vào chính email cấu hình SMTP.

## 5. Giao diện Quản trị (Admin Panel)
- **Sidebar Menu**: Thêm mục **"Quản lý Yêu cầu / Form"** (nên đặt riêng biệt hoặc nằm trong mục Khách hàng/Hệ thống).
- **Danh sách (Index View)**: 
  - Hiển thị bảng dạng DataTable.
  - Các cột: Mã form (#ID), Phân loại (Type), Thông tin tóm tắt (Trích xuất Name/Email từ JSON nếu có), Thời gian gửi, **Trạng thái (Badge màu sắc)**.
  - Bộ lọc thông minh: Lọc theo `status` và `type`.
- **Chi tiết (Show/Edit View)**:
  - Hiển thị toàn bộ thông tin form (Render đẹp mảng JSON thành các trường Label - Value).
  - Khối hành động: Đổi trạng thái xử lý (`Pending` -> `Resolved`), Ghi chú nội bộ của Admin cho form đó (có thể thêm cột `admin_note` vào DB nếu cần).
- **Phân quyền (RBAC)**: Bổ sung các quyền `form_submission.view`, `form_submission.update`, `form_submission.delete` để gắn cho role Quản trị viên hoặc Nhân viên Hỗ trợ.

## 6. Bảo mật & Chống Spam (Security & Anti-Spam)
- **Rate Limiting**: Cấu hình Throttle cho Route API submit form (Ví dụ: 3 form / 10 phút / 1 IP) để tránh kẻ gian dùng tool spam rác vào Database và làm cạn kiệt băng thông gửi Mail SMTP.
- **reCAPTCHA / Turnstile**: Backend cần chuẩn bị sẵn logic verify token từ Google reCAPTCHA hoặc Cloudflare Turnstile để frontend có thể tích hợp.

## 7. Các bước thực hiện tiếp theo (Next Steps)
1. Chạy lệnh tạo Model, Migration, Controller, Resource: `php artisan make:model FormSubmission -mcr`
2. Khai báo cấu trúc bảng trong file Migration và chạy migrate.
3. Tạo FormRequest & Mail class: `php artisan make:mail AdminFormNotificationMail`
4. Cấu hình Route API và Route Admin.
5. Cập nhật Sidebar và tạo các trang Blade (hoặc React/Vue tùy stack admin) cho Quản lý Form.
