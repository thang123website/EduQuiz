<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTarget extends Model
{
    use HasUuidv7;

    protected $fillable = [
        'user_id',
        'target_type',
        'target_score',
        'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    /**
     * User this target belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
