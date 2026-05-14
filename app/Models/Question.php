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
        'parent_id',
        'type',
        'level',
        'content',
        'media_url',
        'media_type',
        'default_mark',
        'explanation',
        'shuffle_options',
    ];

    protected $casts = [
        'shuffle_options' => 'boolean',
        'default_mark' => 'decimal:2',
    ];

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
        return $this->hasMany(Question::class, 'parent_id');
    }

    /**
     * Options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Parts this question belongs to.
     */
    public function parts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(QuizPart::class, 'question_quiz_part', 'question_id', 'part_id')
                    ->withPivot('order_idx', 'mark')
                    ->orderByPivot('order_idx', 'asc');
    }

    /**
     * Tags for this question.
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'question_tag', 'question_id', 'tag_id');
    }
}
