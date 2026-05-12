<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_products', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false)->after('notes');
            $table->text('shared_note')->nullable()->after('is_shared');
        });
    }

    public function down(): void
    {
        Schema::table('plan_products', function (Blueprint $table) {
            $table->dropColumn(['is_shared', 'shared_note']);
        });
    }
};
