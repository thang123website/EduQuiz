<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'caption', 'is_admin'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
