# SKILL 6: SLIDER MODULE (SHOFY/RVMEDIA STANDARD)

> **Tài liệu chuẩn hóa kiến trúc Slider chuyên nghiệp** | Cấp độ: Senior Backend Architect | Áp dụng cho EduQuiz (Laravel 13).

---

## 🎯 1. Thiết kế Cơ sở dữ liệu & Indexing (Database Design)

Tối ưu hóa hiệu năng truy vấn và khả năng tùy biến động.

### Bảng `sliders` (Cấu hình nhóm)
- `id`: Primary Key (ULID/ID)
- `name`: Tên slider.
- `key`: **Unique Key (UK)** - Chuỗi định danh (ví dụ: `home-main-slider`).
- `settings`: **JSON** - Lưu cấu hình Swiper (autoplay, speed, pagination, effect...).
- `status`: Enum (`active`, `inactive`).
- *Index:* `key` (Unique).

### Bảng `slider_items` (Nội dung slide)
- `id`: Primary Key.
- `slider_id`: **Foreign Key** liên kết `sliders`.
- `title`, `image`, `link`, `description`.
- `order`: Integer - Thứ tự sắp xếp.
- `status`: Enum (`active`, `inactive`).
- *Index:* `slider_id`, `order` (Để tối ưu câu lệnh `ORDER BY`).

---

## 🛠️ 2. Kiến trúc Backend (Laravel Standards)

### A. Request Validation (Clean Code)
Sử dụng Form Request để tách biệt logic kiểm tra dữ liệu.
```php
public function rules(): array {
    return [
        'name' => 'required|string|max:255',
        'key'  => 'required|string|unique:sliders,key,' . $this->route('slider')?->id,
        'status' => 'required|in:active,inactive',
        'settings' => 'nullable|array',
    ];
}
```

### B. Service Pattern & Cache Management
Xử lý logic nghiệp vụ tập trung tại `SliderService`.
- **Cache Invalidation:** Sử dụng `Cache::tags(['sliders'])` để xóa cache ngay khi dữ liệu thay đổi.
- **Atomic Updates:** Đảm bảo việc cập nhật thứ tự (Sortable) diễn ra nhanh chóng qua AJAX.

---

## 🖥️ 3. Quản trị UX (Master-Detail Management)

### Quy trình Chỉnh sửa (Single Page Management):
1. **Form Chính:** Sửa thông tin `sliders` (Name, Key, Settings).
2. **Khu vực Items:**
   - Hiển thị danh sách items dạng Table.
   - **Modal Pop-up:** Thêm/Sửa từng item qua Modal để giữ ngữ cảnh trang (không chuyển hướng).
   - **Drag & Drop:** Tích hợp `Sortable.js`. Sau khi kéo thả, gửi mảng ID lên API để cập nhật cột `order`.

---

## 🎨 4. Tích hợp Frontend (Performance & SEO)

### Thư viện: **Swiper.js**
- **Cấu hình Động:** Lấy dữ liệu từ cột `settings` (JSON) để khởi tạo Swiper.
- **Lazy Loading:** `loading="lazy"` cho các slide không phải là slide đầu tiên.
- **SEO:** Tự động điền `alt` từ `title` và `title` từ `description` (nếu có).

---

## ⚠️ CÁC LỖI CẦN TRÁNH
1. **N+1 Query:** Luôn sử dụng `with('items')` khi lấy Slider.
2. **Hard-coded Settings:** Tránh viết cứng cấu hình slider trong file JS/Blade; hãy tận dụng cột `settings`.
3. **No Cache Invalidation:** Quên xóa cache khiến thay đổi trong Admin không hiển thị ở Frontend.
4. **Missing Index:** Thiếu index ở cột `order` khiến việc sắp xếp bị chậm khi dữ liệu lớn.
