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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->unique()->nullable()->after('email');
            $table->boolean('ban')->default(false)->after('status');
            $table->timestamp('ban_start_at')->nullable()->after('ban');
            $table->timestamp('ban_end_at')->nullable()->after('ban_start_at');
            $table->integer('logged_count')->default(0)->after('ban_end_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'ban', 'ban_start_at', 'ban_end_at', 'logged_count']);
        });
    }
};
