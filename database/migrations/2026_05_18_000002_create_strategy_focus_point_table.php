<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategy_focus_point', function (Blueprint $table) {
            $table->foreignId('strategy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('focus_point_id')->constrained()->cascadeOnDelete();
            $table->primary(['strategy_id', 'focus_point_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategy_focus_point');
    }
};
