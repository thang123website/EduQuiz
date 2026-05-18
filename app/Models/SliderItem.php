<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class SliderItem extends Model
{
    use HasTranslations;

    protected $fillable = [
        'slider_id',
        'title',
        'image',
        'link',
        'description',
        'order',
        'status',
        'is_highlight',
    ];

    protected $casts = [
        'is_highlight' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public $translatable = ['title', 'description'];

    /**
     * URL ảnh đầy đủ để hiển thị
     */
    public function getImageUrlAttribute()
    {
        return get_image_url($this->image);
    }

    /**
     * Nhóm slider mà item này thuộc về.
     */
    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }
}
