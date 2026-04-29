<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = ['title', 'slug'];

    /**
     * Danh sách bài viết thuộc danh mục này
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
