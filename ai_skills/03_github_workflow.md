# SKILL 3: TIÊU CHUẨN GIT & GITHUB (INTERNATIONAL STANDARD)

Để dự án EduQuiz thể hiện được sự chuyên nghiệp (Production-ready) trong mắt các nhà tuyển dụng hoặc cộng đồng Open Source, mọi thao tác với Git/GitHub cần tuân thủ bộ quy chuẩn dưới đây:

## 1. Loại bỏ rác trước khi Commit (.gitignore)
TUYỆT ĐỐI KHÔNG push các file sau lên GitHub:
- **Thông tin nhạy cảm:** File `.env` chứa mật khẩu Database. Chỉ để lại file `.env.example`.
- **Thư mục thư viện:** `vendor/` (PHP), `node_modules/` (JS). Người khác clone về sẽ tự chạy `composer install`.
- **File hệ thống & bộ nhớ tạm:** `.DS_Store` (Mac), `Thumbs.db` (Windows), thư mục `storage/framework/views/` (ngoại trừ file `.gitignore` bên trong nó).

## 2. Conventional Commits (Quy tắc viết Message)
Mọi lịch sử commit phải rõ ràng, dễ đọc, cấu trúc chuẩn quốc tế:
`<type>(<scope>): <subject>`

**Các `<type>` bắt buộc dùng:**
- `feat`: Khi thêm một tính năng mới (Ví dụ: `feat(quiz): add timer to exam page`).
- `fix`: Khi sửa lỗi (Ví dụ: `fix(auth): resolve login timeout issue`).
- `docs`: Khi viết/sửa tài liệu README (Ví dụ: `docs: update setup instruction`).
- `style`: Định dạng code (xuống dòng, dấu phẩy...) không ảnh hưởng logic.
- `refactor`: Sửa code nhưng không làm thay đổi tính năng hay sửa lỗi (Ví dụ: `refactor(admin): optimize sidebar loading`).
- `chore`: Các việc lặt vặt như update thư viện, sửa config.

## 3. Quản lý nhánh (Branching Strategy - Git Flow)
Không bao giờ code trực tiếp trên nhánh `main` (hoặc `master`).
- **Nhánh `main`:** Chỉ chứa code ổn định, có thể Deploy ngay lên server.
- **Nhánh `develop`:** Nhánh gộp code để test trước khi đưa lên `main`.
- **Nhánh tính năng (`feature/...`):** Khi làm một chức năng mới. Ví dụ: `git checkout -b feature/admin-dashboard`.
- **Nhánh sửa lỗi (`bugfix/...` hoặc `hotfix/...`):** Khi cần sửa lỗi khẩn cấp.

## 4. Cấu trúc README.md chuẩn chuyên nghiệp
Một repository chuẩn quốc tế phải có file `README.md` bao gồm:
1. **Logo & Tên dự án:** Cùng 1 dòng mô tả ngắn.
2. **Tech Stack:** Các công nghệ sử dụng (Laravel 11, MySQL, Redis, Velzon UI...).
3. **Features:** Danh sách các tính năng nổi bật.
4. **Prerequisites (Điều kiện tiên quyết):** Yêu cầu cài PHP 8.2+, Docker, Composer...
5. **Installation (Hướng dẫn cài đặt):** Các bước chi tiết từ `git clone` đến `php artisan serve`.
6. **License:** Giấy phép mã nguồn mở (MIT, GPL...).

---

### HƯỚNG DẪN THỰC HÀNH PUSH CODE LẦN ĐẦU (INIT)

Mở terminal trong thư mục dự án (`\\wsl$\Ubuntu\home\thang\EduQuiz`) và chạy lần lượt:

```bash
# 1. Khởi tạo Git
git init

# 2. Thêm tất cả file vào giỏ hàng (Git sẽ tự động bỏ qua các file trong .gitignore)
git add .

# 3. Viết commit đầu tiên chuẩn Conventional
git commit -m "chore: initial project setup with Laravel and Velzon Admin UI"

# 4. Đổi tên nhánh thành main (chuẩn mới của Github)
git branch -M main

# 5. Liên kết với Repository trống trên Github (Thay URL của bạn vào)
git remote add origin https://github.com/<tên_của_bạn>/EduQuiz.git

# 6. Đẩy code lên (Lưu ý: Bạn sẽ cần đăng nhập GitHub nếu chưa từng đăng nhập)
git push -u origin main
```
