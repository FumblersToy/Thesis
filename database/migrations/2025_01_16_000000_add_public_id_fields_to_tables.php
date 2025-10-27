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
        // Add image_public_id to posts table
        if (!Schema::hasColumn('posts', 'image_public_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('image_public_id')->nullable()->after('image_path');
            });
        }

        // Add profile_picture_public_id to musicians table
        if (!Schema::hasColumn('musicians', 'profile_picture_public_id')) {
            Schema::table('musicians', function (Blueprint $table) {
                $table->string('profile_picture_public_id')->nullable()->after('profile_picture');
            });
        }

        // Add profile_picture_public_id to businesses table
        if (!Schema::hasColumn('businesses', 'profile_picture_public_id')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->string('profile_picture_public_id')->nullable()->after('profile_picture');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('posts', 'image_public_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('image_public_id');
            });
        }

        if (Schema::hasColumn('musicians', 'profile_picture_public_id')) {
            Schema::table('musicians', function (Blueprint $table) {
                $table->dropColumn('profile_picture_public_id');
            });
        }

        if (Schema::hasColumn('businesses', 'profile_picture_public_id')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropColumn('profile_picture_public_id');
            });
        }
    }
};

