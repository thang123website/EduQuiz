<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasUuidv7;

    const UPDATED_AT = null; // We only track created_at for attempts

    protected $fillable = [
        'user_id',
        'quiz_id',
        'part_ids',
        'score',
        'correct_count',
        'total_count',
        'time_spent',
        'status',
    ];

    protected $casts = [
        'part_ids' => 'array',
    ];

    /**
     * User who made the attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quiz attempted.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Specific responses in this attempt.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(UserResponse::class, 'attempt_id');
    }

    /**
     * Get names of parts for this attempt.
     */
    public function getPartNamesAttribute()
    {
        if (empty($this->part_ids)) {
            return '';
        }
        return \App\Models\QuizPart::whereIn('id', $this->part_ids)->pluck('title')->implode(', ');
    }
}
