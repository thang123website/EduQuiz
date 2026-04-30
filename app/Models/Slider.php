<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends Model
{
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
