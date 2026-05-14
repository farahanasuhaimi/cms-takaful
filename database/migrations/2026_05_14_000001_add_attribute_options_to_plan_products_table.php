<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_products', function (Blueprint $table) {
            $table->json('attribute_options')->nullable()->after('attributes');
        });
    }

    public function down(): void
    {
        Schema::table('plan_products', function (Blueprint $table) {
            $table->dropColumn('attribute_options');
        });
    }
};
