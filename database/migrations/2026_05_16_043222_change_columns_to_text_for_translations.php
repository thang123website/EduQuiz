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
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
        });

        Schema::table('blog', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('slider_items', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('blog', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('description')->nullable()->change();
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('description')->nullable()->change();
        });

        Schema::table('slider_items', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('description')->nullable()->change();
        });
    }
};
