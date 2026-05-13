<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('prospect_name')->nullable()->after('notes');
            $table->string('prospect_phone')->nullable()->after('prospect_name');
            $table->text('prospect_notes')->nullable()->after('prospect_phone');
        });
    }
};
