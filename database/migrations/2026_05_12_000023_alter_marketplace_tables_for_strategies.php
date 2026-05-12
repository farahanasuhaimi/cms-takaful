<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->foreignId('strategy_id')->nullable()->after('angle_content_id')
                  ->constrained()->nullOnDelete();
        });

        // Make angle_content_id nullable — raw SQL to avoid doctrine/dbal dependency
        DB::statement('ALTER TABLE marketplace_listings MODIFY COLUMN angle_content_id BIGINT UNSIGNED NULL');

        Schema::table('marketplace_purchases', function (Blueprint $table) {
            $table->foreignId('imported_strategy_id')->nullable()->after('imported_content_id')
                  ->constrained('strategies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_purchases', function (Blueprint $table) {
            $table->dropForeign(['imported_strategy_id']);
            $table->dropColumn('imported_strategy_id');
        });

        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->dropForeign(['strategy_id']);
            $table->dropColumn('strategy_id');
        });

        DB::statement('ALTER TABLE marketplace_listings MODIFY COLUMN angle_content_id BIGINT UNSIGNED NOT NULL');
    }
};
