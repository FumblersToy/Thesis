<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_path_2')->nullable();
            $table->string('image_path_3')->nullable();
            $table->string('image_public_id')->nullable();
            $table->string('image_public_id_2')->nullable();
            $table->string('image_public_id_3')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};