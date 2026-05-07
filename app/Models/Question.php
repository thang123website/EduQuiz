<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasUuidv7;

    protected $fillable = [
        'quiz_id',
        'part_id',
        'parent_id',
        'type',
        'content',
        'media_url',
        'media_type',
        'grade',
        'explanation',
        'shuffle_options',
        'order_idx',
    ];

    protected $casts = [
        'shuffle_options' => 'boolean',
        'grade' => 'decimal:2',
    ];

    /**
     * Quiz this question belongs to.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Parent question (for TOEIC grouping).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'parent_id');
    }

    /**
     * Sub-questions (for TOEIC grouping).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Question::class, 'parent_id')->orderBy('order_idx');
    }

    /**
     * Options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Part this question belongs to.
     */
    public function part(): BelongsTo
    {
        return $this->belongsTo(QuizPart::class, 'part_id');
    }
}
