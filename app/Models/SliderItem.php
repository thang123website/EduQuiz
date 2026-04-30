<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderItem extends Model
{
    protected $fillable = [
        'slider_id',
        'title',
        'image',
        'link',
        'description',
        'order',
        'status',
    ];

    /**
     * Nhóm slider mà item này thuộc về.
     */
    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }
}
