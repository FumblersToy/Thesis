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
        Schema::table('musicians', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musicians', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_name']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_name']);
        });
    }
};
