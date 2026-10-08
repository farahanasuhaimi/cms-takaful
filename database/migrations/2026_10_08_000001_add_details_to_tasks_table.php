<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Auto-cards take due_date from their signal (follow-up date, lead's
            // Next Contact); manual cards set it in the detail sheet.
            $table->date('due_date')->nullable()->after('status_changed_at');
            $table->boolean('is_priority')->default(false)->after('due_date');
            $table->text('notes')->nullable()->after('is_priority');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'is_priority', 'notes']);
        });
    }
};
