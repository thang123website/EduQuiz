<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        "user_id",
        "parent_id",
        "commentable_id",
        "commentable_type",
        "content",
        "status",
        "ip_address",
        "user_agent",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, "parent_id");
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, "parent_id")->where("status", "active")->orderBy("created_at", "asc");
    }

    public function allReplies()
    {
        return $this->hasMany(Comment::class, "parent_id")->orderBy("created_at", "asc");
    }

    public function scopeActive($query)
    {
        return $query->where("status", "active");
    }

    public function scopePending($query)
    {
        return $query->where("status", "pending");
    }
}
