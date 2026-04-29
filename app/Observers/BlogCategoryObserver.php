<?php

namespace App\Observers;

use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogCategoryObserver
{
    /**
     * Tự động tạo slug trước khi lưu danh mục mới
     */
    public function creating(BlogCategory $category): void
    {
        if (empty($category->slug)) {
            $category->slug = $this->generateUniqueSlug($category->title);
        }
    }

    /**
     * Tự động cập nhật slug khi tên thay đổi
     */
    public function updating(BlogCategory $category): void
    {
        if ($category->isDirty('title') && empty($category->slug)) {
            $category->slug = $this->generateUniqueSlug($category->title);
        }
    }

    /**
     * Tạo slug duy nhất, thêm số đếm nếu bị trùng
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = BlogCategory::where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
