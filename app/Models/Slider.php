<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Slider extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'key',
        'description',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public $translatable = ['name', 'description'];

    /**
     * Danh sách các slide items thuộc nhóm này.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SliderItem::class)->orderBy('order');
    }

    /**
     * Chỉ lấy các items đang active.
     */
    public function activeItems(): HasMany
    {
        return $this->hasMany(SliderItem::class)
            ->where('status', 'active')
            ->orderBy('order');
    }
}
