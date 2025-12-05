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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
            $table->text('deletion_reason')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deletion_scheduled_at')->nullable();
            $table->enum('appeal_status', ['none', 'pending', 'approved', 'denied'])->default('none');
            $table->text('appeal_message')->nullable();
            $table->timestamp('appeal_at')->nullable();
            $table->text('appeal_response')->nullable();
            
            $table->foreign('deleted_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn([
                'deleted_at',
                'deletion_reason',
                'deleted_by',
                'deletion_scheduled_at',
                'appeal_status',
                'appeal_message',
                'appeal_at',
                'appeal_response'
            ]);
        });
    }
};
