<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->foreignId('strategy_id')->nullable()->after('angle_content_id')
                  ->constrained()->nullOnDelete();
            $table->change('angle_content_id', function (Blueprint $col) {});
        });

        // Make angle_content_id nullable (separate statement for compatibility)
        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->unsignedBigInteger('angle_content_id')->nullable()->change();
        });

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
            $table->unsignedBigInteger('angle_content_id')->nullable(false)->change();
        });
    }
};
