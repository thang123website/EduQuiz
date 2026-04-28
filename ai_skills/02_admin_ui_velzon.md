# SKILL 2: TIÊU CHUẨN GIAO DIỆN ADMIN (VELZON) - PRODUCTION READY

Khi thiết kế hoặc tích hợp giao diện Admin từ template HTML thuần (Velzon) vào Laravel, AI BẮT BUỘC phải tuân thủ các quy tắc chuẩn Senior "10/10" dưới đây để đảm bảo tính tái sử dụng cao, tốc độ load trang cực nhanh và cấu trúc thư mục tối ưu.

## 1. Smart Asset Management (Quản lý CSS/JS tối ưu)
- **Không Bundle thư viện khổng lồ:** Phục vụ file tĩnh (CSS/JS/Images) lõi của Velzon từ thư mục `public/assets/admin/`. Vite chỉ dùng cho Custom Code.
- **Sử dụng Stacks:** Đảm bảo thứ tự ưu tiên (Z-index, Override CSS) luôn đúng bằng cách sử dụng `@stack`.
  - Tại Master Layout: BẮT BUỘC đặt `@stack('plugin-styles')` trước CSS core và `@stack('plugin-scripts')` trước JS core.
- **Lazy Loading Partials:** Đối với các phần dữ liệu động nhưng không quan trọng ngay lúc đầu (như Top bar notifications), phải dùng AJAX (hoặc Livewire) để load ngầm sau khi trang chính đã load xong, giúp giảm TTFB (Time to First Byte).

## 2. Kiến trúc Blade Component (UI Reusability)
- Không chỉ dừng ở việc "chặt" Master Layout thành Partials, BẮT BUỘC phải thiết kế **Blade Components** cho các thành phần UI nhỏ thường xuyên lặp lại (Button, Card, Input).
- **Ví dụ Component Button Submit có Loading State:**
  ```html
  {{-- resources/views/components/admin/button-submit.blade.php --}}
  <button type="submit" {{ $attributes->merge(['class' => 'btn btn-primary btn-load']) }}>
      <span class="d-flex align-items-center">
          <span class="flex-grow-1 me-2">{{ $slot }}</span>
          <span class="spinner-border flex-shrink-0 d-none" role="status">
              <span class="visually-hidden">Loading...</span>
          </span>
      </span>
  </button>
  ```

## 3. Menu Active Logic (Clean Code)
- TUYỆT ĐỐI KHÔNG viết logic `request()->routeIs(...)` rải rác khắp nơi làm file Sidebar dài và khó đọc.
- BẮT BUỘC tạo Helper function hoặc View Composer để xử lý. 
- Khuyến nghị dùng Helper `NavigationHelper.php`:
  ```php
  function is_active_route($routePatterns, $class = 'active') {
      return request()->routeIs($routePatterns) ? $class : '';
  }
  ```

## 4. Image Optimization (Tối ưu Ảnh)
- Tốc độ load cực kỳ quan trọng cho trải nghiệm quản trị.
- BẮT BUỘC phải cấu hình tự động resize/optimize các ảnh người dùng upload (avatar, thumbnail bài thi, hình ảnh câu hỏi) bằng các package như `Spatie Media Library` hoặc `Intervention Image`. 
- Tuyệt đối không load ảnh gốc dung lượng MB ra trang danh sách (List View).

## 5. Cấu trúc thư mục "Final Standard"
Tuân thủ tuyệt đối sơ đồ cây thư mục sau để dễ dàng Team-work:
```text
resources/views/admin/
├── layouts/
│   ├── master.blade.php
│   ├── head-css.blade.php
│   ├── vendor-scripts.blade.php
│   └── partials/           # Các mảnh nhỏ hơn (Header, Sidebar)
│       ├── topbar.blade.php
│       ├── sidebar.blade.php
│       └── footer.blade.php
├── components/             # Các UI Reusable (button-submit, input...)
├── quizzes/                # Đi kèm với QuizController
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── users/                  # Đi kèm với UserController
```
