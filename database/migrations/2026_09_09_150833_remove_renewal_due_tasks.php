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
        // Renewals stay on the Dashboard only now — drop any auto-backlog
        // cards TaskAutoBacklogService created for them before this change.
        DB::table('tasks')->where('source_type', 'renewal_due')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Deleted rows aren't meaningfully restorable.
    }
};
