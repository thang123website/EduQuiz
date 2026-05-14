<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizPart extends Model
{
    use HasUuidv7;

    protected $fillable = [
        'quiz_id',
        'title',
        'description',
        'order_idx',
    ];

    /**
     * Quiz this part belongs to.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Questions in this part.
     */
    public function questions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_quiz_part', 'part_id', 'question_id')
                    ->withPivot('order_idx', 'mark')
                    ->orderByPivot('order_idx', 'asc');
    }
}
