<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slider_id')->constrained('sliders')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Index để ORDER BY chạy nhanh khi slider nhiều items
            $table->index(['slider_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_items');
    }
};
