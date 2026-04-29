<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';

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
}
