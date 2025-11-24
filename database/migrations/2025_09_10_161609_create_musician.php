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
        Schema::create('musicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('profile_picture')->nullable();
            $table->string('profile_picture_public_id')->nullable();  // ADD THIS LINE
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('stage_name')->nullable();
            $table->string('genre')->nullable();
            $table->string('genre2')->nullable();
            $table->string('genre3')->nullable();
            $table->string('instrument')->nullable();
            $table->string('instrument2')->nullable();
            $table->string('instrument3')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->text('bio')->nullable();
            $table->string('credential_document')->nullable();
            $table->string('credential_document_public_id')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musicians');
    }
};