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
        Schema::create("comments", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->foreignUlid("user_id")->constrained("users")->onDelete("cascade");
            $table->ulid("parent_id")->nullable(); 
            
            $table->ulid("commentable_id");
            $table->string("commentable_type");
            
            $table->text("content");
            $table->enum("status", ["pending", "active", "hidden"])->default("pending");
            $table->string("ip_address")->nullable();
            $table->string("user_agent")->nullable();
            
            $table->timestamps();
            
            $table->index(["commentable_id", "commentable_type"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("comments");
    }
};
