<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->after('lead_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
            $table->dropConstrainedForeignId('client_id');
        });
    }
};
