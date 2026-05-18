<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BlogCategory extends Model
{
    use HasTranslations;

    protected $fillable = ['title', 'slug', 'parent_id'];

    public $translatable = ['title'];

    /**
     * Danh mục cha
     */
    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    /**
     * Danh sách danh mục con
     */
    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    /**
     * Lấy cây danh mục đã được làm phẳng (flattened) có kèm thuộc tính level
     */
    public static function getTree()
    {
        $allCategories = self::withCount('blogs')->get();
        return self::buildTree($allCategories);
    }

    private static function buildTree($categories, $parentId = null, $level = 0)
    {
        $result = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $category->level = $level;
                $result[] = $category;
                $result = array_merge($result, self::buildTree($categories, $category->id, $level + 1));
            }
        }
        return $result;
    }

    /**
     * Danh sách bài viết thuộc danh mục này
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
