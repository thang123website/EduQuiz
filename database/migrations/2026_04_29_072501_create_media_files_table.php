<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            // String vì User dùng HasUlids
            $table->string('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('name');
            $table->string('alt')->nullable();
            $table->string('url');                         // đường dẫn tương đối trong storage
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0); // bytes
            $table->string('type')->default('image');      // image, video, document
            $table->string('visibility')->default('public');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
