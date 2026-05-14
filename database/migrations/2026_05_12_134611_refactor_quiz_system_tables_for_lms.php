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
        // 1. Alter quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->enum('type', ['full_test', 'practice', 'minitest'])->default('full_test')->after('pass_mark');
        });

        // 2. Alter questions table
        Schema::table('questions', function (Blueprint $table) {
            // Drop foreign keys before dropping columns
            $table->dropForeign(['quiz_id']);
            $table->dropForeign(['part_id']);
            
            // Drop columns
            $table->dropColumn(['quiz_id', 'part_id', 'order_idx']);

            // Rename grade to default_mark
            $table->renameColumn('grade', 'default_mark');
            
            // Add level
            $table->enum('level', ['easy', 'medium', 'hard', 'very_hard'])->default('medium')->after('type');
        });

        // 3. Create question_quiz_part
        Schema::create('question_quiz_part', function (Blueprint $table) {
            $table->uuid('part_id');
            $table->uuid('question_id');
            $table->integer('order_idx')->default(0);
            $table->decimal('mark', 5, 2)->nullable()->comment('Override default mark if needed');
            
            $table->primary(['part_id', 'question_id']);
            $table->foreign('part_id')->references('id')->on('quiz_parts')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        // 4. Create question_tag
        Schema::create('question_tag', function (Blueprint $table) {
            $table->uuid('question_id');
            $table->uuid('tag_id');
            
            $table->primary(['question_id', 'tag_id']);
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_tag');
        Schema::dropIfExists('question_quiz_part');

        Schema::table('questions', function (Blueprint $table) {
            $table->uuid('quiz_id')->after('id')->nullable(); // nullable to prevent errors on rollback if existing rows
            $table->uuid('part_id')->nullable()->after('quiz_id');
            $table->integer('order_idx')->default(0)->after('shuffle_options');
            
            $table->dropColumn('level');
            $table->renameColumn('default_mark', 'grade');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
