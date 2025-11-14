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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->string('profile_picture')->nullable();
            $table->string('profile_picture_public_id')->nullable();
            $table->string('business_name')->nullable();
            $table->string('contact_email')->nullable()->unique();
            $table->string('phone_number', 20)->nullable();
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('venue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business');
    }
};
