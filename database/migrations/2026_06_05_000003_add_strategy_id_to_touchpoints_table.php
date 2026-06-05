<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('touchpoints', function (Blueprint $table) {
            $table->foreignId('strategy_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('touchpoints', function (Blueprint $table) {
            $table->dropConstrainedForeignId('strategy_id');
        });
    }
};
