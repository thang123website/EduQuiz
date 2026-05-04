<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'role_name', 'status', 'latitude', 'longitude', 'address', 'gender', 'dob', 'avatar', 'cover_photo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlids;

    protected $appends = [
        'avatar_url'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $permissions = null;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role_id && $this->role && $this->role->is_admin;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return get_image_url($this->avatar);
        }

        return route('default-avatar', [
            'id' => $this->id,
            'name' => $this->name
        ]);
    }

    public function hasPermission($permissionName)
    {
        if ($this->permissions === null) {
            if (!$this->role_id) {
                $this->permissions = [];
                return false;
            }

            $this->permissions = \Illuminate\Support\Facades\Cache::remember("user_permissions_{$this->role_id}", 3600, function () {
                return \App\Models\Permission::where('role_id', $this->role_id)
                    ->where('allow', true)
                    ->join('sections', 'permissions.section_id', '=', 'sections.id')
                    ->pluck('sections.name')
                    ->toArray();
            });
        }

        return in_array($permissionName, $this->permissions);
    }
}
