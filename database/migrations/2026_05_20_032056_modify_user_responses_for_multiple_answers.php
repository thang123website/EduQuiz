<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_responses', function (Blueprint $table) {
            // Thêm unique index mới để tránh lưu trùng lặp cùng 1 option cho 1 câu hỏi
            // Index này bắt đầu bằng attempt_id nên sẽ thay thế index cho foreign key
            $table->unique(['attempt_id', 'question_id', 'selected_option_id'], 'user_resp_att_q_opt_unique');
        });

        Schema::table('user_responses', function (Blueprint $table) {
            // Xóa unique index cũ giới hạn 1 đáp án cho 1 câu hỏi
            $table->dropUnique(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_responses', function (Blueprint $table) {
            $table->dropUnique('user_resp_att_q_opt_unique');
            
            // Cảnh báo: Việc khôi phục lại sẽ báo lỗi nếu có dữ liệu đã bị trùng (nhiều đáp án/1 câu).
            // Tạm thời tạo lại unique index cũ.
            $table->unique(['attempt_id', 'question_id']);
        });
    }
};
