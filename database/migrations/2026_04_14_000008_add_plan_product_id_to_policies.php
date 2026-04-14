<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->foreignId('plan_product_id')
                  ->nullable()
                  ->after('client_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\PlanProduct::class);
            $table->dropColumn('plan_product_id');
        });
    }
};
