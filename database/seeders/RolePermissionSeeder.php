<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Section;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder {
    public function run(): void {
        // 1. Tạo Roles
        $adminRole = Role::create(['name' => 'admin', 'caption' => 'Quản trị viên', 'is_admin' => true]);
        $teacherRole = Role::create(['name' => 'teacher', 'caption' => 'Giáo viên', 'is_admin' => false]);
        $studentRole = Role::create(['name' => 'student', 'caption' => 'Học sinh', 'is_admin' => false]);

        // 2. Tạo Sections (Quyền)
        $sections = [
            ['name' => 'admin_exams_list', 'caption' => 'Xem danh sách bài thi', 'group' => 'exams'],
            ['name' => 'admin_exams_create', 'caption' => 'Tạo bài thi mới', 'group' => 'exams'],
            ['name' => 'admin_exams_edit', 'caption' => 'Chỉnh sửa bài thi', 'group' => 'exams'],
            ['name' => 'admin_users_manage', 'caption' => 'Quản lý người dùng', 'group' => 'users'],
        ];

        foreach ($sections as $sec) {
            $section = Section::create($sec);

            // Gán quyền cho Teacher (Chỉ được xem và tạo bài thi, không được quản lý User)
            if (str_contains($sec['name'], 'exams')) {
                Permission::create([
                    'role_id' => $teacherRole->id,
                    'section_id' => $section->id,
                    'allow' => true
                ]);
            }
        }
    }
}
