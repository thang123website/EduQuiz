<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bot_configs', function (Blueprint $table) {
            $table->id();
            $table->string('bot_code', 50)->unique();
            $table->text('system_instruction');
            $table->float('temperature')->default(0.2); // Thấp để chính xác cao
            $table->json('response_schema')->nullable(); // Lưu trữ JSON Schema động nếu cần
            $table->timestamps();
        });

        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('bot_config_id')->constrained('bot_configs');
            $table->string('session_token', 64)->unique();
            $table->timestamps();

            $table->index(['user_id', 'session_token']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['user', 'model', 'system']);
            $table->text('content');
            $table->integer('tokens_used')->nullable();
            $table->timestamps();

            // Tối ưu hóa việc load lịch sử chat theo thứ tự thời gian
            $table->index(['chat_session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('bot_configs');
    }
};
