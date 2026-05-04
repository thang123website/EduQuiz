<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationHistory extends Model
{
    protected $fillable = [
        'title',
        'body',
        'audience_type',
        'channels',
        'image',
        'url',
        'sender_id',
        'user_count',
    ];

    protected $casts = [
        'channels' => 'array',
    ];

    /**
     * URL ảnh đầy đủ để hiển thị (xử lý cả đường dẫn tương đối và tuyệt đối)
     */
    public function getImageUrlAttribute()
    {
        return get_image_url($this->image);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
