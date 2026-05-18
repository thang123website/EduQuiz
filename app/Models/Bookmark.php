<?php

namespace App\Models;

use App\Traits\HasUuidv7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    use HasUuidv7;

    protected $fillable = [
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
    ];

    /**
     * Get the parent bookmarkable model (Quiz or Blog).
     */
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who bookmarked it.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
