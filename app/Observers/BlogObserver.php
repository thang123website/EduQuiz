<?php

namespace App\Observers;

use App\Models\Blog;
use Illuminate\Support\Str;

class BlogObserver
{
    /**
     * Tự động tạo slug trước khi lưu bài viết mới
     */
    public function creating(Blog $blog): void
    {
        if (empty($blog->slug)) {
            $blog->slug = $this->generateUniqueSlug($blog->title);
        }
    }

    /**
     * Tự động cập nhật slug khi tiêu đề thay đổi
     */
    public function updating(Blog $blog): void
    {
        if ($blog->isDirty('title') && empty($blog->slug)) {
            $blog->slug = $this->generateUniqueSlug($blog->title);
        }
    }

    /**
     * Tạo slug duy nhất, thêm số đếm nếu bị trùng
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = Blog::where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
