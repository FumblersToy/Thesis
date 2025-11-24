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
            $table->string('instagram_url')->nullable()->after('bio');
            $table->string('facebook_url')->nullable()->after('instagram_url');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('youtube_url')->nullable()->after('twitter_url');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('address');
            $table->string('facebook_url')->nullable()->after('instagram_url');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('website_url')->nullable()->after('twitter_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musicians', function (Blueprint $table) {
            $table->dropColumn(['instagram_url', 'facebook_url', 'twitter_url', 'youtube_url']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['instagram_url', 'facebook_url', 'twitter_url', 'website_url']);
        });
    }
};
