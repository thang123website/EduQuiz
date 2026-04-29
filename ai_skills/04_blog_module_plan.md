# SKILL 4: KẾ HOẠCH TRIỂN KHAI BLOG MODULE — EDUQUIZ

> **Phân tích từ source code thực tế của dự án** | Tuân thủ kiến trúc RBAC + Observer + Cache hiện có

---

## 🗂️ Tổng Quan Kiến Trúc

```
EduQuiz/
├── Database
│   ├── Migrations
│   │   ├── create_blog_categories_table.php
│   │   └── create_blog_table.php
│   └── Seeders
│       └── RolePermissionSeeder.php  (cập nhật thêm quyền blog)
├── App
│   ├── Models
│   │   ├── BlogCategory.php
│   │   └── Blog.php
│   ├── Observers
│   │   ├── BlogObserver.php          (tự tạo slug)
│   │   └── BlogCategoryObserver.php  (tự tạo slug)
│   └── Http/Controllers/Admin
│       ├── BlogCategoryController.php
│       └── BlogController.php
├── Routes
│   └── web.php                       (thêm resource routes)
└── Resources/Views/Admin
    ├── blog-categories/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── blog/
        ├── index.blade.php
        ├── create.blade.php
        └── edit.blade.php
```

---

## ⚠️ LƯU Ý QUAN TRỌNG (Đặc thù dự án EduQuiz)

- **`author_id` PHẢI là `string`** vì model `User` dùng `HasUlids`. Dùng `foreignId()` sẽ bị lỗi kiểu dữ liệu ngay.
- Dùng `Gate::authorize()` theo đúng pattern trong `RoleController` và `UserController`.
- Slug được tạo tự động qua **Observer** (theo pattern `PermissionObserver` đã có).
- Đăng ký Observer trong `AppServiceProvider::boot()`.

---

## 📊 Phase 1: Database

### Migration `blog_categories`

```php
Schema::create('blog_categories', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->timestamps();
});
```

### Migration `blog`

```php
Schema::create('blog', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')
          ->nullable()
          ->constrained('blog_categories')
          ->nullOnDelete();
    // PHẢI dùng string vì User dùng HasUlids (không phải auto-increment)
    $table->string('author_id');
    $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
    $table->string('title');
    $table->string('slug')->unique();
    $table->string('image')->nullable();
    $table->text('description')->nullable();
    $table->longText('content');
    $table->unsignedInteger('visit_count')->default(0);
    $table->boolean('enable_comment')->default(true);
    $table->enum('status', ['pending', 'publish'])->default('pending');
    $table->timestamps();
});
```

---

## 🏛️ Phase 2: Models

### `BlogCategory.php`

```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = ['title', 'slug'];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
```

### `Blog.php`

```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';
    protected $fillable = [
        'category_id', 'author_id', 'title', 'slug',
        'image', 'description', 'content',
        'visit_count', 'enable_comment', 'status',
    ];
    protected $casts = ['enable_comment' => 'boolean'];

    public function category()   { return $this->belongsTo(BlogCategory::class, 'category_id'); }
    public function author()     { return $this->belongsTo(User::class, 'author_id'); }
}
```

### `BlogObserver.php` (Tự tạo Slug)

```php
namespace App\Observers;
use App\Models\Blog;
use Illuminate\Support\Str;

class BlogObserver
{
    public function creating(Blog $blog): void
    {
        if (empty($blog->slug)) {
            $slug = Str::slug($blog->title);
            $count = Blog::where('slug', 'like', "{$slug}%")->count();
            $blog->slug = $count > 0 ? "{$slug}-{$count}" : $slug;
        }
    }
}
```

---

## 🔐 Phase 3: RBAC — Thêm vào `RolePermissionSeeder.php`

```php
// Thêm vào mảng $sections:
['name' => 'blog.view',            'caption' => 'Xem danh sách bài viết',  'group' => 'blog'],
['name' => 'blog.create',          'caption' => 'Tạo bài viết mới',         'group' => 'blog'],
['name' => 'blog.update',          'caption' => 'Chỉnh sửa bài viết',       'group' => 'blog'],
['name' => 'blog.delete',          'caption' => 'Xoá bài viết',             'group' => 'blog'],
['name' => 'blog_category.view',   'caption' => 'Xem danh mục blog',        'group' => 'blog'],
['name' => 'blog_category.create', 'caption' => 'Tạo danh mục mới',         'group' => 'blog'],
['name' => 'blog_category.update', 'caption' => 'Chỉnh sửa danh mục',       'group' => 'blog'],
['name' => 'blog_category.delete', 'caption' => 'Xoá danh mục',             'group' => 'blog'],
```

---

## 🚦 Phase 4: Routes (`web.php`)

```php
// Thêm vào trong group admin:
Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class);
Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class);
```

---

## 🎮 Phase 5: Controllers

### `BlogCategoryController.php` — Pattern chuẩn theo `UserController`

```php
public function index()   { Gate::authorize('blog_category.view');   ... }
public function create()  { Gate::authorize('blog_category.create'); ... }
public function store()   { Gate::authorize('blog_category.create'); ... }
public function edit()    { Gate::authorize('blog_category.update'); ... }
public function update()  { Gate::authorize('blog_category.update'); ... }
public function destroy() { Gate::authorize('blog_category.delete'); ... }
```

### `BlogController.php` — Xử lý thêm upload ảnh

```php
public function store(Request $request) {
    // validate + upload image vào storage/public/blog
    // lưu với author_id = auth()->id()
    // slug tự tạo qua BlogObserver
}
public function destroy(Blog $blog) {
    // Xóa file ảnh khỏi storage trước khi xóa bản ghi
    if ($blog->image) Storage::delete($blog->image);
    $blog->delete();
}
```

---

## 🖥️ Phase 6: Views (Chuẩn Velzon)

### Layout Form `blog/create.blade.php` — 2 cột

| Cột trái (col-lg-8) | Cột phải (col-lg-4) |
|---------------------|---------------------|
| Title | Upload ảnh thumbnail |
| Slug (auto, readonly) | Danh mục (select) |
| Content (textarea/editor) | Trạng thái (select: pending/publish) |
| Mô tả ngắn | Cho phép bình luận (Custom Switch) |

### Cột Trạng Thái trong `blog/index.blade.php`

```blade
@if($blog->status === 'publish')
    <span class="badge bg-success-subtle text-success">Đã xuất bản</span>
@else
    <span class="badge bg-warning-subtle text-warning">Chờ duyệt</span>
@endif
```

---

## 📦 Phase 7: Đăng ký Observer

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    \App\Models\Blog::observe(\App\Observers\BlogObserver::class);
    \App\Models\BlogCategory::observe(\App\Observers\BlogCategoryObserver::class);
}
```

---

## 🗺️ Lộ Trình Thực Hiện

| # | Phase | Nội dung | Trạng thái |
|---|-------|----------|------------|
| 1 | Database | 2 migration files | ⬜ Chưa làm |
| 2 | Models | `Blog.php`, `BlogCategory.php`, 2 Observers | ⬜ Chưa làm |
| 3 | RBAC | Cập nhật `RolePermissionSeeder.php` + chạy seed | ⬜ Chưa làm |
| 4 | Routes | Cập nhật `web.php` | ⬜ Chưa làm |
| 5 | Controllers | `BlogController.php`, `BlogCategoryController.php` | ⬜ Chưa làm |
| 6 | Views | 6 file Blade (index, create, edit × 2) | ⬜ Chưa làm |
| 7 | Provider | Đăng ký Observer trong `AppServiceProvider.php` | ⬜ Chưa làm |

**Git commit message:** `feat(blog): add blog module with categories, RBAC, and slug observer`
