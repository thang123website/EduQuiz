<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quiz extends Model
{
    use HasUuidv7;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'thumbnail',
        'duration',
        'pass_mark',
        'difficulty',
        'status',
        'is_new',
        'is_popular',
        'question_count',
        'total_points',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
    ];

    /**
     * Category this quiz belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class, 'category_id');
    }

    /**
     * Questions in this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order_idx');
    }

    /**
     * Attempts made for this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Tags for this quiz.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'quiz_tag');
    }

    /**
     * Parts in this quiz.
     */
    public function parts(): HasMany
    {
        return $this->hasMany(QuizPart::class)->orderBy('order_idx');
    }
}
