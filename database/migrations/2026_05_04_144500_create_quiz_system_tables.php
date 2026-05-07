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
        // 1. Categories Table (Unified Recursive Tree)
        Schema::create('quiz_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 50)->default('academic'); // toeic, k12, academic, etc.
            $table->string('icon', 100)->nullable();
            $table->integer('order_idx')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('quiz_categories')->onDelete('cascade');
        });

        // 2. Quizzes Table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->integer('duration')->comment('Minutes');
            $table->integer('pass_mark')->default(50);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('status', 20)->default('draft'); // draft, published, archived
            $table->boolean('is_new')->default(true);
            $table->boolean('is_popular')->default(false);
            
            // Denormalized fields for performance
            $table->integer('question_count')->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
            
            $table->json('settings')->nullable(); // For flexible configurations
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('quiz_categories')->onDelete('cascade');
        });

        // 2.1 Tags Table
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2.2 Quiz-Tag Intermediary Table
        Schema::create('quiz_tag', function (Blueprint $table) {
            $table->uuid('quiz_id');
            $table->uuid('tag_id');
            
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->primary(['quiz_id', 'tag_id']);
        });

        // 2.3 Quiz Parts Table
        Schema::create('quiz_parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_idx')->default(0);
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });

        // 3. Questions Table
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->uuid('part_id')->nullable();
            $table->uuid('parent_id')->nullable(); // For TOEIC grouping (passage/audio)
            $table->string('type', 50)->default('single_choice');
            $table->text('content')->comment('Markdown support');
            $table->text('media_url')->nullable();
            $table->string('media_type', 20)->default('none'); // image, audio, none
            $table->decimal('grade', 5, 2)->default(1.00);
            $table->text('explanation')->nullable();
            $table->boolean('shuffle_options')->default(true);
            $table->integer('order_idx')->default(0);
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('part_id')->references('id')->on('quiz_parts')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('questions')->onDelete('cascade');
        });

        // 4. Options Table
        Schema::create('options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('question_id');
            $table->text('text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        // 5. Quiz Attempts Table (User History)
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('quiz_id');
            $table->decimal('score', 5, 2)->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->integer('time_spent')->comment('Seconds');
            $table->string('status', 20)->default('completed'); // completed, failed, passed
            $table->timestamp('created_at')->nullable();

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            // user_id linked to users table (assuming uuid primary key for users as well)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 6. User Responses Table (Review Data)
        Schema::create('user_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('attempt_id');
            $table->uuid('question_id');
            $table->uuid('selected_option_id')->nullable();
            $table->boolean('is_correct')->default(false); // Denormalized for speed
            $table->timestamps();

            $table->foreign('attempt_id')->references('id')->on('quiz_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('options')->onDelete('set null');

            $table->unique(['attempt_id', 'question_id']);
        });

        // 7. User Targets Table (Tracking Goals)
        Schema::create('user_targets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('target_type', 50)->default('toeic'); // toeic, ielts, etc.
            $table->integer('target_score');
            $table->date('exam_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_targets');
        Schema::dropIfExists('user_responses');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quiz_parts');
        Schema::dropIfExists('quiz_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('quiz_categories');
    }
};
