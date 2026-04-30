# SKILL 6: SLIDER MODULE (SHOFY/RVMEDIA STANDARD)

> **Tài liệu chuẩn hóa kiến trúc Slider chuyên nghiệp** | Dựa trên phân tích Shofy/RvMedia, áp dụng cho EduQuiz (Laravel 13).

---

## 🎯 1. Thiết kế Cơ sở dữ liệu (Database Design)

Tách biệt "Cấu hình nhóm" và "Nội dung slide" để tối ưu hóa quản lý và mở rộng.

### Bảng `sliders` (Nhóm Slider)
- `id`: Primary Key (ULID/ID)
- `name`: Tên slider (ví dụ: "Trang chủ - Banner chính")
- `key`: Chuỗi định danh duy nhất (ví dụ: `home-main-slider`) -> **BẮT BUỘC dùng key để gọi ở Frontend**.
- `description`: Mô tả ngắn (tùy chọn).
- `status`: Trạng thái (`active`, `inactive`).
- `created_at`, `updated_at`.

### Bảng `slider_items` (Chi tiết từng Slide)
- `id`: Primary Key (ULID/ID)
- `slider_id`: Foreign Key liên kết với bảng `sliders`.
- `title`: Tiêu đề hiển thị trên slide (hỗ trợ SEO).
- `image`: Đường dẫn ảnh (phải liên kết với Media Manager nếu có).
- `link`: URL điều hướng khi click (ví dụ: `/products/abc`).
- `description`: Nội dung mô tả ngắn trên slide.
- `order`: Thứ tự sắp xếp (mặc định 0).
- `status`: Trạng thái (`active`, `inactive`).
- `created_at`, `updated_at`.

---

## 🛠️ 2. Quy trình Quản trị (Admin Workflow - UX/UI)

### Trang Danh sách (Index)
- Hiển thị danh sách các **Slider Group**.
- Tìm kiếm theo `name` hoặc `key`.

### Trang Chỉnh sửa (Edit Slider Group - Single Page Management)
- **Khu vực 1 (Thông tin chung):** Sửa Name, Key, Status của nhóm.
- **Khu vực 2 (Danh sách Items):** 
  - Hiển thị Table các Slide Items thuộc nhóm đó.
  - **Modal Form:** Nút "Thêm Slide" hoặc "Sửa" sẽ mở Modal (không chuyển trang).
  - **Kéo thả (Sortable):** Sử dụng `Sortable.js` để thay đổi thứ tự trực tiếp trên bảng.
  - **Lưu thứ tự:** Hiện nút "Save Sorting" sau khi kéo thả để cập nhật cột `order` qua AJAX.

---

## ⚙️ 3. Logic xử lý (Backend Logic)

- **Query tối ưu:** Luôn load `slider_items` kèm theo `sliders` và sắp xếp theo `order ASC`.
- **Hàm hỗ trợ (Helper/Service):**
  ```php
  function get_slider_by_key($key) {
      return Slider::where('key', $key)
                   ->where('status', 'active')
                   ->with(['items' => function($q) {
                       $q->where('status', 'active')->orderBy('order', 'asc');
                   }])
                   ->first();
  }
  ```
- **Xóa Cascading:** Khi xóa `sliders`, tự động xóa sạch `slider_items` liên quan để tránh rác database.
- **Caching:** Cache kết quả `get_slider_by_key`. Xóa cache khi có bất kỳ thay đổi nào trong Admin.

---

## 🎨 4. Tích hợp Frontend (Frontend Integration)

### Thư viện khuyến nghị: **Swiper.js** (Hiện đại, hiệu năng cao).

### Quy tắc hiển thị:
- **Lazy Loading:** Chỉ load ảnh slide đầu tiên ngay lập tức. Các slide tiếp theo dùng `loading="lazy"`.
- **Responsive Images:** Sử dụng các phiên bản ảnh nhỏ hơn cho Mobile (nếu hệ thống Media hỗ trợ resize).
- **Accessibility (A11y):** Luôn điền thuộc tính `alt` cho ảnh từ trường `title`.
- **Cấu hình động:** Nếu cần, có thể thêm trường `settings` (JSON) vào bảng `sliders` để lưu cấu hình Swiper (tốc độ, hiệu ứng transition).

---

## ⚠️ CÁC LỖI CẦN TRÁNH
1. Gọi slider bằng `id` cứng trong code Frontend (Gây lỗi khi migrate dữ liệu).
2. Không xử lý upload ảnh tập trung qua Media Manager.
3. Chuyển trang quá nhiều khi quản lý từng item (Gây ức chế cho người dùng).
