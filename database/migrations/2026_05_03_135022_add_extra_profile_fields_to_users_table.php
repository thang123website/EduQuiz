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
        Schema::table("users", function (Blueprint $table) {
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->string("address")->nullable();
            $table->enum("gender", ["male", "female", "other"])->nullable();
            $table->date("dob")->nullable();
            $table->string("avatar")->nullable();
            $table->string("cover_photo")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn(["latitude", "longitude", "address", "gender", "dob", "avatar", "cover_photo"]);
        });
    }
};
