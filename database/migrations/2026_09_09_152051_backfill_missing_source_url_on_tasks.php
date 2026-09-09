<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Auto-cards created before source_url existed are stuck with a
        // NULL link forever — TaskAutoBacklogService only creates missing
        // cards, it never updates existing ones. Remove the active
        // (non-dismissed) ones so the next board load recreates them fresh,
        // this time with a working source_url.
        DB::table('tasks')
            ->whereNotNull('source_type')
            ->whereNull('source_url')
            ->whereNull('deleted_at')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Deleted rows aren't meaningfully restorable — they'll be
        // recreated by TaskAutoBacklogService on the next board load anyway.
    }
};
