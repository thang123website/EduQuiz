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
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'part_id')->orderBy('order_idx');
    }
}
