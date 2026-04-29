<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('blog_categories')
                  ->nullOnDelete();
            // PHẢI dùng string vì User model dùng HasUlids (không phải auto-increment int)
            $table->string('author_id');
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->longText('content');
            $table->unsignedInteger('visit_count')->default(0);
            $table->boolean('enable_comment')->default(true);
            $table->enum('status', ['pending', 'publish'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog');
    }
};
