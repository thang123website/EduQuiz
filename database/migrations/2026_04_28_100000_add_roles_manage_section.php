<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Thêm section quản lý phân quyền nếu chưa có
        if (!DB::table('sections')->where('name', 'admin_roles_manage')->exists()) {
            DB::table('sections')->insert([
                'name'       => 'admin_roles_manage',
                'caption'    => 'Quản lý phân quyền',
                'group'      => 'users',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('sections')->where('name', 'admin_roles_manage')->delete();
    }
};
