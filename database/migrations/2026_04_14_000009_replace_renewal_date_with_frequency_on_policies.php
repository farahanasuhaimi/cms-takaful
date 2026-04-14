<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('renewal_date');
            $table->enum('frequency', ['monthly', 'yearly'])->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('frequency');
            $table->date('renewal_date')->nullable()->after('start_date');
        });
    }
};
