<?php

namespace App\Observers;

use App\Models\Permission;
use Illuminate\Support\Facades\Cache;

class PermissionObserver {
    public function saved(Permission $permission) {
        $this->clearCache($permission);
    }

    public function deleted(Permission $permission) {
        $this->clearCache($permission);
    }

    protected function clearCache(Permission $permission) {
        // Xóa cache danh sách quyền của Role bị ảnh hưởng
        Cache::forget("user_permissions_{$permission->role_id}");
    }
}
