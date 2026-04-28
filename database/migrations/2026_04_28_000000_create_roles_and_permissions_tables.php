<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Bảng Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, teacher, student, organization
            $table->string('caption'); // Tên hiển thị tiếng Việt: Quản trị viên, Giáo viên...
            $table->boolean('is_admin')->default(false)->index(); 
            $table->timestamps();
        });

        // 2. Bảng Sections (Danh sách các hành động/quyền)
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Ví dụ: admin_exams_create
            $table->string('caption'); // Ví dụ: Tạo mới bài thi
            $table->string('group')->index(); // Gom nhóm để hiển thị: exams, users, settings
            $table->timestamps();
        });

        // 3. Bảng Permissions (Bảng trung gian Role - Section)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->boolean('allow')->default(true);
            $table->timestamps();

            // Composite Index: Tăng tốc độ query check quyền cực nhanh
            $table->unique(['role_id', 'section_id']);
        });

        // 4. Cập nhật bảng Users (Thêm cột liên kết Role)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
            $table->string('role_name')->nullable()->after('role_id');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'role_name']);
        });
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('roles');
    }
};
