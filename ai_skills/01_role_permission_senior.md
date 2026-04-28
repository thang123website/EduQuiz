# SKILL 1: HỆ THỐNG PHÂN QUYỀN (ROLE & PERMISSION) CHUẨN SENIOR

Để đảm bảo hệ thống phân quyền của EduQuiz chịu tải tốt (10k+ users), tính đóng gói (Encapsulation), hiệu suất (Performance) và dễ bảo trì (Testability), AI phải tuân thủ nghiêm ngặt các nguyên tắc sau khi code module Phân quyền:

## 1. Database Migrations (Tối ưu Index)
- Bắt buộc phải có **Composite Index** (Unique) cho các bảng trung gian (Pivot) để query cực nhanh.
- Ví dụ bảng `permissions`:
  ```php
  $table->unique(['role_id', 'section_id']);
  ```
- Bảng `sections` phải có trường `group` để gom nhóm hiển thị trên UI.
- Bảng `roles` phải có cờ `is_admin` để nhận diện Super Admin dễ dàng.

## 2. Base Model & Trait (Tái sử dụng code)
- KHÔNG viết logic phân quyền trực tiếp vào `User.php`.
- BẮT BUỘC phải tạo và sử dụng Trait `HasPermissions` (trong `App\Traits`).
- Logic trong hàm `hasPermission()`:
  1. Kiểm tra Super Admin (`is_admin`) đầu tiên để bypass.
  2. Sử dụng **Local Cache** (`Cache::remember`) để lưu quyền của một Role. Thời gian cache có thể là 3600s.

## 3. AuthServiceProvider (Dynamic Gate Mapping)
- Việc đăng ký Gate phải được thực hiện linh hoạt (Dynamic) thay vì hardcode.
- Sử dụng Cache để lưu toàn bộ danh sách `sections` (`all_sections_name`) khi boot hệ thống.
- Sử dụng `method_exists($user, 'hasPermission')` để check an toàn trước khi gọi hàm.

## 4. Xử lý Cache Invalidation (Bắt buộc)
- Khi quyền (Permission) của một Role thay đổi (Thêm/Xóa/Sửa), BẮT BUỘC phải xóa Cache ngay lập tức.
- Yêu cầu dùng **Model Observers** (ví dụ: `PermissionObserver`) móc vào sự kiện `saved` / `deleted` để chạy `Cache::forget()`. Không được xóa cache thủ công rải rác ở Controller.

## 5. Áp dụng vào Controller (Clean Code)
- Hạn chế dùng `if/else` thủ công với Gate.
- Ưu tiên dùng hàm `$this->authorize('section_name')` trong Controller để tự động ném ra lỗi 403.
- Đối với Middleware bảo vệ route, có thể viết Custom Middleware `CheckRole` để bảo vệ nguyên một group route.

## 6. Naming Convention & Seeder
- Đặt tên section theo cấu trúc `module_action` (ví dụ: `quiz_create`, `user_edit`).
- Bắt buộc tạo `RolesAndPermissionsSeeder` chứa sẵn bộ khung quyền chuẩn để deploy dễ dàng.
