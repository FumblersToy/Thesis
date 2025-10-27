<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('musicians', function (Blueprint $table) {
            if (!Schema::hasColumn('musicians', 'instrument')) {
                $table->string('instrument')->nullable()->after('genre');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'venue')) {
                $table->string('venue')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('musicians', function (Blueprint $table) {
            if (Schema::hasColumn('musicians', 'instrument')) {
                $table->dropColumn('instrument');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'venue')) {
                $table->dropColumn('venue');
            }
        });
    }
}; 