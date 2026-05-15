# KẾ HOẠCH TRIỂN KHAI HỆ THỐNG ĐA NGÔN NGỮ (MULTI-LANGUAGE) CHO EDUQUIZ

Tài liệu này vạch ra lộ trình chi tiết để xây dựng kiến trúc Đa ngôn ngữ chuẩn Enterprise (tương tự Stackfood) cho nền tảng EduQuiz, áp dụng tách biệt rạch ròi giữa Backend (Laravel) và Frontend (Next.js/React).

---

## GIAI ĐOẠN 1: CẤU TRÚC DATABASE (BACKEND LARAVEL)

Hệ thống cần quản lý 2 loại dữ liệu song song: **Cài đặt ngôn ngữ hệ thống** và **Dữ liệu nội dung động** (Ví dụ: Bài viết, Danh mục, Đề thi).

### 1. Quản lý Danh sách Ngôn ngữ Hệ thống
Thay vì tạo một bảng `languages` rời rạc, chúng ta sẽ lưu tập trung vào bảng cấu hình chung (`settings` hoặc `business_settings`) dưới dạng mảng JSON.
- **Key:** `system_language`
- **Value (JSON):**
  ```json
  [
    {"id":1, "code":"vi", "name":"Tiếng Việt", "direction":"ltr", "status":1, "default":true},
    {"id":2, "code":"en", "name":"English", "direction":"ltr", "status":1, "default":false}
  ]
  ```

### 2. Quản lý Nội dung Động (Dynamic Content)
Với các bảng nội dung như `blog`, `blog_categories`, `quizzes`, `questions`, v.v...
👉 **Thư viện khuyên dùng:** `spatie/laravel-translatable`

- **Cách hoạt động:** Các cột văn bản (ví dụ: `title`, `description`, `content`) sẽ được chuyển sang định dạng JSON trong MySQL.
  - Ví dụ DB lưu: `{"vi": "Bài viết lập trình", "en": "Programming Blog"}`
- **Tích hợp Model:**
  ```php
  use Spatie\Translatable\HasTranslations;

  class Blog extends Model {
      use HasTranslations;
      public $translatable = ['title', 'description', 'content'];
  }
  ```
- Khi API được gọi, Laravel tự động bóc tách và trả về chuỗi văn bản khớp với ngôn ngữ hiện tại (`App::getLocale()`).

---

## GIAI ĐOẠN 2: API & LOGIC QUẢN TRỊ (ADMIN PANEL)

Xây dựng `LanguageController` trong Admin Panel để quản trị viên (Admin) vận hành từ vựng tĩnh (Static texts).

### 1. Thêm ngôn ngữ mới
- Khi Admin thêm ngôn ngữ `en`, Backend tự động copy file từ vựng mặc định `resources/lang/vi.json` thành `resources/lang/en.json`.
- (Khuyên dùng chuẩn `.json` để Frontend dễ dàng parse và đồng bộ qua API).
- Thêm đối tượng ngôn ngữ vào key `system_language` trong DB.

### 2. Danh sách Key - Value (Quản lý từ vựng tĩnh)
- **API `translate_list`:** Đọc file `resources/lang/{code}.json` và phân trang trả về danh sách Key-Value để Admin điền bản dịch.
- VD: `{"login_btn": "Đăng nhập", "home": "Trang chủ"}`.

### 3. Cập nhật bản dịch thủ công
- Nhận payload từ Admin: `{ "key": "login_btn", "value": "Vào hệ thống", "lang": "vi" }`.
- Backend tự động parse file `{lang}.json`, sửa đổi value theo key tương ứng và lưu đè lại.

### 4. Dịch Tự Động bằng AI/Google (Auto Translate)
👉 **Thư viện khuyên dùng:** `stichoza/google-translate-php`
- Admin nhấn "Auto Translate" cho ngôn ngữ mới.
- Hệ thống quyét file `.json`, phát hiện các Value chưa được dịch và đẩy qua Google Translate API.
- **Xử lý Timeout:** Sử dụng thuật toán `chunk` (chia nhỏ 20-50 từ/lần request) và cập nhật `%` tiến độ lên giao diện Admin giống hệt cơ chế Stackfood.

---

## GIAI ĐOẠN 3: LOGIC MIDDLEWARE & GIAO TIẾP API

Để Backend biết Frontend đang gọi dữ liệu bằng ngôn ngữ nào, cần thiết lập kết nối song phương thông qua Headers.

### 1. Middleware Localization
Tạo `App\Http\Middleware\LocalizationMiddleware`:
```php
public function handle($request, Closure $next)
{
    // Bắt header X-localization từ Frontend, mặc định là 'vi'
    $local = $request->header('X-localization', 'vi'); 
    
    // Set ngôn ngữ toàn cục cho request hiện tại
    \App::setLocale($local);
    
    return $next($request);
}
```
*Đăng ký Middleware này bao bọc toàn bộ các `routes/api.php` của hệ thống.*

### 2. API Cấp phát từ vựng cho Frontend
Tạo Endpoint: `GET /api/v1/translations/{lang}`
- Nhiệm vụ: Trả thẳng nguyên cục JSON từ file `resources/lang/{lang}.json` về cho ứng dụng Frontend tải nội dung tĩnh ban đầu.

---

## GIAI ĐOẠN 4: TÍCH HỢP FRONTEND (NEXT.JS / REACT)

Frontend chịu trách nhiệm render từ vựng tĩnh và gắn cờ (header) vào mỗi request gọi dữ liệu động.

### 1. Dịch UI Tĩnh (Static Texts)
👉 **Thư viện khuyên dùng:** `next-intl` (dành cho Next.js) hoặc `react-i18next`.
- Khi App khởi động (hoặc người dùng bấm đổi cờ ngôn ngữ), App sẽ fetch `/api/v1/translations/{lang}`.
- Đổ dữ liệu JSON đó vào thư viện. Các text tĩnh trong code được thay bằng: `<button>{t('login_btn')}</button>`.

### 2. Cấu hình Axios / Fetch Client (Gửi Header)
Tự động đính kèm `X-localization` vào mọi Request gọi lên Backend:
```javascript
// Gắn chặt X-localization vào mọi request gửi đi
axios.interceptors.request.use(config => {
  const currentLang = localStorage.getItem('lang') || 'vi'; // Lấy từ LocalStorage hoặc Cookie
  config.headers['X-localization'] = currentLang;
  return config;
});
```

---

## 🎯 TỔNG KẾT QUY TRÌNH LUỒNG ĐI THỰC TẾ

1. Người dùng vào EduQuiz, bấm vào icon 🇬🇧 (Tiếng Anh). Frontend lưu trạng thái `lang='en'` vào Cookie/LocalStorage.
2. Frontend lập tức gọi API `/api/v1/translations/en`, nhận cục JSON và đổi giao diện tĩnh (Login -> Sign in).
3. Người dùng truy cập trang danh sách Bài viết, Frontend gọi `GET /api/v1/blogs` kèm theo Header `X-localization: en`.
4. Middleware của Laravel bắt được Header, set `App::setLocale('en')`.
5. Trong `BlogController`, do Model `Blog` dùng thư viện `laravel-translatable`, lúc xuất JSON nó tự động trích xuất chuỗi Tiếng Anh từ DB.
6. Kết quả: Toàn bộ từ tĩnh lẫn dữ liệu động đều đồng bộ hoàn hảo sang Tiếng Anh. Khi rảnh rỗi, Admin có thể tự thêm Tiếng Hàn, Tiếng Nhật... chỉ bằng 1 cú click "Auto Translate" trong Dashboard. 🚀
