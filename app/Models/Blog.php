<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Blog extends Model
{
    use HasTranslations;

    protected $table = 'blog';

    public $translatable = ['title', 'description', 'content'];

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'image',
        'description',
        'content',
        'visit_count',
        'enable_comment',
        'status',
    ];

    protected $casts = [
        'enable_comment' => 'boolean',
    ];

    /**
     * URL ảnh đầy đủ để hiển thị (xử lý cả đường dẫn tương đối và tuyệt đối)
     */
    public function getImageUrlAttribute()
    {
        return get_image_url($this->image);
    }

    /**
     * Danh mục của bài viết
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Tác giả bài viết (liên kết User qua author_id dạng ULID string)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    /**
     * Danh sách bình luận của bài viết
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    /**
     * Chỉ lấy các bình luận đã duyệt
     */
    public function activeComments()
    {
        return $this->comments()->where('status', 'active');
    }
}
