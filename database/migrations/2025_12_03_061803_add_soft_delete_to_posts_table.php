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
        Schema::table('posts', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
            $table->text('deletion_reason')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->enum('appeal_status', ['none', 'pending', 'approved', 'denied'])->default('none');
            $table->text('appeal_message')->nullable();
            $table->timestamp('appeal_at')->nullable();
            
            // Foreign key for admin who deleted the post
            $table->foreign('deleted_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn([
                'deleted_at',
                'deletion_reason',
                'deleted_by',
                'appeal_status',
                'appeal_message',
                'appeal_at'
            ]);
        });
    }
};
