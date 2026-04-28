<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'caption', 'group'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
