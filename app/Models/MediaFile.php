<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'folder_id', 'name', 'alt',
        'url', 'mime_type', 'size', 'type', 'visibility',
    ];

    protected $appends = ['full_url', 'thumb_url', 'formatted_size'];

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * URL đầy đủ để hiển thị ảnh gốc
     */
    public function getFullUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->url);
    }

    /**
     * URL thumbnail medium
     */
    public function getThumbUrlAttribute(): string
    {
        if ($this->type !== 'image') {
            return asset('assets/images/file-icons/' . pathinfo($this->url, PATHINFO_EXTENSION) . '.png');
        }

        $dir      = dirname($this->url);
        $base     = pathinfo($this->url, PATHINFO_FILENAME);
        $ext      = pathinfo($this->url, PATHINFO_EXTENSION);
        $thumbPath = ($dir !== '.' ? $dir . '/' : '') . $base . '-thumb.' . $ext;

        if (Storage::disk('public')->exists($thumbPath)) {
            return Storage::disk('public')->url($thumbPath);
        }
        return $this->full_url;
    }

    /**
     * Dung lượng file dạng đọc được (KB, MB)
     */
    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;
        if ($size >= 1048576) return round($size / 1048576, 2) . ' MB';
        if ($size >= 1024)    return round($size / 1024, 2) . ' KB';
        return $size . ' B';
    }
}
