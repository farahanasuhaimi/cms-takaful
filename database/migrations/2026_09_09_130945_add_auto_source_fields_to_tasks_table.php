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
        Schema::table('tasks', function (Blueprint $table) {
            // Tags a task as auto-generated from a CRM signal (overdue follow-up,
            // renewal due, hot lead) so it isn't recreated once resolved or dismissed.
            $table->string('source_type')->nullable()->after('status');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->softDeletes();

            $table->index(['user_id', 'source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'source_type', 'source_id']);
            $table->dropColumn(['source_type', 'source_id', 'deleted_at']);
        });
    }
};
