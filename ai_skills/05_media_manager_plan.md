# SKILL 5: MEDIA MANAGER MODULE — EDUQUIZ

> **Tài liệu chuẩn hóa từ phân tích globo-edu + Shofy/RvMedia** | Áp dụng cho dự án EduQuiz trên Laravel 13

---

## 🎯 Tổng Quan Kiến Trúc

Module Media Manager gồm **2 phần độc lập nhưng liên kết**:

1. **Backend**: Upload, lưu trữ, xử lý ảnh (resize, compress), quản lý thư mục
2. **Frontend**: UI dạng Grid (lưới ảnh), chọn ảnh cho Blog/bài viết qua Modal Popup

### Sơ đồ quan hệ Database

```
media_folders (1) ──── (N) media_files
users         (1) ──── (N) media_files
users         (1) ──── (N) media_folders
```

---

## ⚠️ LƯU Ý ĐẶC THÙ DỰ ÁN EDUQUIZ

- **`user_id` PHẢI là `string`** vì User model dùng `HasUlids`. KHÔNG dùng `foreignId()`.
- `intervention/image` cần PHP extension `gd` hoặc `imagick`. Kiểm tra trong container Docker.
- Chạy `php artisan storage:link` trong container trước khi test upload.
- Blog hiện tại đang dùng **`file upload` trực tiếp** (khác globo-edu). Cần refactor để dùng `string` path từ Media Manager.

---

## 📊 Phase 1: Database (2 Migrations)

### Migration `media_folders`

```php
Schema::create('media_folders', function (Blueprint $table) {
    $table->id();
    $table->string('user_id')->nullable();
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    $table->unsignedBigInteger('parent_id')->nullable(); // thư mục cha (self-referencing)
    $table->string('name');
    $table->string('slug')->unique();
    $table->softDeletes();
    $table->timestamps();
});
```

### Migration `media_files`

```php
Schema::create('media_files', function (Blueprint $table) {
    $table->id();
    $table->string('user_id')->nullable();
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
    $table->string('name');
    $table->string('alt')->nullable();
    $table->string('url');
    $table->string('mime_type')->nullable();
    $table->unsignedBigInteger('size')->default(0);
    $table->string('type')->default('image'); // image, video, document
    $table->string('visibility')->default('public');
    $table->softDeletes();
    $table->timestamps();
});
```

---

## 🏛️ Phase 2: Models

### `MediaFolder.php`

```php
class MediaFolder extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'parent_id', 'name', 'slug'];

    public function files() { return $this->hasMany(MediaFile::class, 'folder_id'); }
    public function parent() { return $this->belongsTo(MediaFolder::class, 'parent_id'); }
    public function children() { return $this->hasMany(MediaFolder::class, 'parent_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
}
```

### `MediaFile.php`

```php
class MediaFile extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'folder_id', 'name', 'alt', 'url', 'mime_type', 'size', 'type', 'visibility'];

    public function folder() { return $this->belongsTo(MediaFolder::class, 'folder_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }

    public function getUrlAttribute($value) { return Storage::disk('public')->url($value); }
    public function getRawUrlAttribute() { return $this->attributes['url']; }
}
```

---

## 🔧 Phase 3: MediaService

File: `app/Services/MediaService.php`

**Các constants:**
```php
const ALLOWED_MIMES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
const THUMBNAIL_SIZES = [
    'thumb'  => [150, 150],
    'medium' => [400, 300],
];
```

**Logic chính của `handleUpload()`:**
1. Validate extension
2. Tạo đường dẫn thư mục `YYYY/MM/` (hoặc theo folder)
3. Tạo tên file unique `slug-timestamp.ext`
4. Xử lý ảnh: nén, giới hạn max width 1920px
5. Lưu file gốc vào `Storage::disk('public')`
6. Tạo thumbnail `-thumb` và `-medium`
7. Lưu metadata vào bảng `media_files`
8. Trả về `['error' => false, 'data' => $mediaFile, 'url' => $url]`

---

## 🔐 Phase 4: RBAC — Thêm vào `RolePermissionSeeder`

```php
['name' => 'media.view',   'caption' => 'Xem thư viện media',    'group' => 'media'],
['name' => 'media.upload', 'caption' => 'Upload file media',      'group' => 'media'],
['name' => 'media.delete', 'caption' => 'Xóa file media',         'group' => 'media'],
```

---

## 🚦 Phase 5: Routes

```php
// Trong group admin:
Route::prefix('media')->name('media.')->group(function () {
    Route::get('/',            [MediaController::class, 'index'])->name('index');
    Route::post('/upload',     [MediaController::class, 'upload'])->name('upload');
    Route::get('/files',       [MediaController::class, 'files'])->name('files');    // API lấy danh sách
    Route::delete('/{file}',   [MediaController::class, 'destroy'])->name('destroy');
    Route::post('/folders',    [MediaController::class, 'createFolder'])->name('folders.store');
});
```

---

## 🎮 Phase 6: MediaController

```php
// index()   → Hiển thị trang Media Manager UI
// upload()  → API nhận file, gọi MediaService::handleUpload(), trả JSON
// files()   → API lấy danh sách files (cho modal picker), trả JSON
// destroy() → Xóa file + thumbnails + bản ghi DB
// createFolder() → Tạo thư mục mới
```

---

## 🖥️ Phase 7: Views (2 loại)

### 7a. Trang Media Manager (`admin/media/index.blade.php`)
- Grid ảnh 6 cột, mỗi ô hiển thị thumbnail + tên file + dung lượng
- Sidebar trái: danh sách thư mục (cây)
- Thanh công cụ: nút Upload, tạo thư mục, tìm kiếm, filter theo loại
- Kéo file vào để upload (drag & drop)

### 7b. Modal Picker (partial `admin/media/picker-modal.blade.php`)
- Gọi từ form Blog/bài viết khi bấm nút "Chọn ảnh"
- Hiển thị grid ảnh, cho phép chọn 1 ảnh
- Khi xác nhận → trả đường dẫn về form cha qua JS callback

---

## 🔗 Phase 8: Tích hợp vào Blog

**Thay đổi BlogController:**
- `store()` và `update()`: Thay `$request->file('image')` thành `$request->input('image')` (string path)
- Bỏ import `Storage` khỏi BlogController (không cần nữa, MediaService lo)

**Thay đổi Views Blog:**
- Thay `<input type="file">` bằng nút "Chọn ảnh" → mở Modal Picker
- Thêm `<input type="hidden" name="image">` để lưu path

---

## 📦 Phase 9: Cài đặt Package

```bash
docker-compose exec app composer require intervention/image
```

Sau khi cài, kiểm tra PHP có extension `gd`:
```bash
docker-compose exec app php -m | grep gd
```

---

## 🗺️ Lộ Trình Thực Hiện

| # | Phase | File cần tạo/sửa | Ưu tiên |
|---|-------|-----------------|---------|
| 1 | Cài package | `composer require intervention/image` | 🔴 Cao |
| 2 | Database | `create_media_folders_table`, `create_media_files_table` | 🔴 Cao |
| 3 | Models | `MediaFile.php`, `MediaFolder.php` | 🔴 Cao |
| 4 | Service | `app/Services/MediaService.php` | 🔴 Cao |
| 5 | RBAC | Cập nhật `RolePermissionSeeder.php` | 🟡 Trung bình |
| 6 | Routes | Cập nhật `web.php` | 🟡 Trung bình |
| 7 | Controller | `MediaController.php` | 🔴 Cao |
| 8 | Views | `media/index.blade.php`, `media/picker-modal.blade.php` | 🔴 Cao |
| 9 | Sidebar | Thêm menu Media vào `sidebar.blade.php` | 🟡 Trung bình |
| 10 | Blog refactor | Sửa `BlogController`, `blog/create.blade.php`, `blog/edit.blade.php` | 🟡 Trung bình |

**Git commit message:** `feat(media): add media manager with upload, folders, and blog integration`
