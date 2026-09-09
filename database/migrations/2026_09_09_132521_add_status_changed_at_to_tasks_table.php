<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // When the task last entered its current status — used to detect
            // Today/Doing cards left over from a previous day.
            $table->timestamp('status_changed_at')->nullable()->after('position');
        });

        // Backfill: best-effort assume existing rows entered their current
        // status the last time they were touched, so this migration doesn't
        // instantly bounce every in-flight Today/Doing card back to Backlog.
        DB::table('tasks')->update(['status_changed_at' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status_changed_at');
        });
    }
};
