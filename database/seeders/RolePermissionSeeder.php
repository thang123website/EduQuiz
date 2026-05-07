<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Section;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder {
    public function run(): void {
        // 1. Tạo Roles
        $adminRole = Role::updateOrCreate(['name' => 'admin'], ['caption' => 'Quản trị viên', 'is_admin' => true]);
        $teacherRole = Role::updateOrCreate(['name' => 'teacher'], ['caption' => 'Giáo viên', 'is_admin' => false]);
        $studentRole = Role::updateOrCreate(['name' => 'student'], ['caption' => 'Học sinh', 'is_admin' => false]);

        // 2. Tạo Sections (Quyền)
        $sections = [
            ['name' => 'exams.view',            'caption' => 'Xem danh sách bài thi',   'group' => 'exams'],
            ['name' => 'exams.create',          'caption' => 'Tạo bài thi mới',          'group' => 'exams'],
            ['name' => 'exams.update',          'caption' => 'Chỉnh sửa bài thi',        'group' => 'exams'],
            ['name' => 'exams.delete',          'caption' => 'Xoá bài thi',              'group' => 'exams'],
            // Quiz Categories permissions
            ['name' => 'quiz_category.view',    'caption' => 'Xem danh mục bài thi',     'group' => 'exams'],
            ['name' => 'quiz_category.create',  'caption' => 'Tạo danh mục bài thi',     'group' => 'exams'],
            ['name' => 'quiz_category.update',  'caption' => 'Chỉnh sửa danh mục bài thi','group' => 'exams'],
            ['name' => 'quiz_category.delete',  'caption' => 'Xoá danh mục bài thi',     'group' => 'exams'],
            // Quiz Attempts permissions
            ['name' => 'quiz_attempt.view',     'caption' => 'Xem lịch sử thi',          'group' => 'exams'],
            ['name' => 'quiz_attempt.delete',   'caption' => 'Xoá lịch sử thi',          'group' => 'exams'],
            ['name' => 'users.view',            'caption' => 'Xem danh sách người dùng', 'group' => 'users'],
            ['name' => 'users.create',          'caption' => 'Thêm người dùng mới',      'group' => 'users'],
            ['name' => 'users.update',          'caption' => 'Chỉnh sửa người dùng',     'group' => 'users'],
            ['name' => 'users.delete',          'caption' => 'Xoá người dùng',           'group' => 'users'],
            ['name' => 'roles.view',            'caption' => 'Xem danh sách phân quyền', 'group' => 'users'],
            ['name' => 'roles.create',          'caption' => 'Thêm phân quyền mới',      'group' => 'users'],
            ['name' => 'roles.update',          'caption' => 'Chỉnh sửa phân quyền',     'group' => 'users'],
            ['name' => 'roles.delete',          'caption' => 'Xoá phân quyền',           'group' => 'users'],
            // Blog permissions
            ['name' => 'blog.view',             'caption' => 'Xem danh sách bài viết',   'group' => 'blog'],
            ['name' => 'blog.create',           'caption' => 'Tạo bài viết mới',         'group' => 'blog'],
            ['name' => 'blog.update',           'caption' => 'Chỉnh sửa bài viết',       'group' => 'blog'],
            ['name' => 'blog.delete',           'caption' => 'Xoá bài viết',             'group' => 'blog'],
            ['name' => 'blog_category.view',    'caption' => 'Xem danh mục blog',        'group' => 'blog'],
            ['name' => 'blog_category.create',  'caption' => 'Tạo danh mục mới',         'group' => 'blog'],
            ['name' => 'blog_category.update',  'caption' => 'Chỉnh sửa danh mục',       'group' => 'blog'],
            ['name' => 'blog_category.delete',  'caption' => 'Xoá danh mục',             'group' => 'blog'],
            // Media permissions
            ['name' => 'media.view',            'caption' => 'Xem thư viện media',        'group' => 'media'],
            ['name' => 'media.upload',          'caption' => 'Upload file media',          'group' => 'media'],
            ['name' => 'media.delete',          'caption' => 'Xoá file media',             'group' => 'media'],
            // Notification permissions
            ['name' => 'notifications.history', 'caption' => 'Xem lịch sử thông báo',     'group' => 'notifications'],
            ['name' => 'notifications.create',  'caption' => 'Gửi thông báo hệ thống',    'group' => 'notifications'],
            // Settings permissions
            ['name' => 'setting.manage',        'caption' => 'Cấu hình hệ thống',         'group' => 'settings'],
        ];

        foreach ($sections as $sec) {
            $section = Section::updateOrCreate(['name' => $sec['name']], $sec);

            // Gán quyền cho Teacher (Chỉ được xem và tạo bài thi, không được quản lý User)
            if (str_contains($sec['name'], 'exams')) {
                Permission::updateOrCreate(
                    [
                        'role_id' => $teacherRole->id,
                        'section_id' => $section->id,
                    ],
                    [
                        'allow' => true
                    ]
                );
            }
        }
    }
}
