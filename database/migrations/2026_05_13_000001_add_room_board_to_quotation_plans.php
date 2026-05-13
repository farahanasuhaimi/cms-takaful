<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_plans', function (Blueprint $table) {
            $table->string('room_board')->nullable()->after('coverage');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_plans', function (Blueprint $table) {
            $table->dropColumn('room_board');
        });
    }
};
