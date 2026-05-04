<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class NotificationAudienceService
{
    /**
     * Get users based on the audience type.
     *
     * @param string $type
     * @param mixed $targetId
     * @return Collection
     */
    public function getUsersByType(string $type, $targetId = null): Collection
    {
        $users = match ($type) {
            'all' => User::all(),
            'students' => User::where('role_name', 'Học sinh')->orWhere('role_name', 'student')->get(),
            'admins' => User::where('role_name', 'Quản trị viên')->orWhere('role_name', 'admin')->get(),
            'single' => User::where('id', $targetId)->get(),
            default => collect([]),
        };

        \Log::info("Audience identified for type {$type}: " . $users->count() . " users.");
        return $users;
    }

    /**
     * Get available audience types for the UI.
     *
     * @return array
     */
    public function getAvailableTypes(): array
    {
        return [
            'all' => 'Tất cả người dùng',
            'students' => 'Tất cả học sinh',
            'admins' => 'Tất cả quản trị viên',
            'single' => 'Người dùng cụ thể',
        ];
    }
}
