<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizCategory extends Model
{
    use HasUuidv7;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type',
        'icon',
        'order_idx',
        'is_active',
    ];

    /**
     * Parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class, 'parent_id');
    }

    /**
     * Subcategories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(QuizCategory::class, 'parent_id')->orderBy('order_idx');
    }

    /**
     * Quizzes in this category.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'category_id');
    }
}
