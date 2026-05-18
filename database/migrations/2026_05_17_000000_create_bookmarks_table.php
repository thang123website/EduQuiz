<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('bookmarkable_type');
            $table->string('bookmarkable_id'); // String to support both UUIDs (Quiz) and Integers (Blog)
            $table->timestamps();

            // A user can only bookmark a specific item once
            $table->unique(['user_id', 'bookmarkable_id', 'bookmarkable_type'], 'user_bookmark_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
