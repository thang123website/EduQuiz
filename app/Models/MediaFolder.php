<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MediaFolder extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'parent_id', 'name', 'slug'];

    public function files()
    {
        return $this->hasMany(MediaFile::class, 'folder_id');
    }

    public function parent()
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = self::withTrashed()->where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
