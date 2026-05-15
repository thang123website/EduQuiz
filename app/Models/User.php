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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'mobile', 'password', 'role_id', 'role_name', 'status', 'ban', 'ban_start_at', 'ban_end_at', 'logged_count', 'latitude', 'longitude', 'address', 'gender', 'dob', 'avatar', 'cover_photo', 'timezone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

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

    public function hasAnyPermission()
    {
        if ($this->permissions === null) {
            // Gọi tạm hasPermission để nạp cache vào $this->permissions
            $this->hasPermission('dummy_load');
        }

        return count($this->permissions) > 0;
    }

    /**
     * Targets set by user.
     */
    public function targets(): HasMany
    {
        return $this->hasMany(UserTarget::class);
    }
}
