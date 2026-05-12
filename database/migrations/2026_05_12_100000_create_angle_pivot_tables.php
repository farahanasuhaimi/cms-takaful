<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angle_lead', function (Blueprint $table) {
            $table->foreignId('reach_angle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->timestamp('linked_at')->useCurrent();
            $table->primary(['reach_angle_id', 'lead_id']);
        });

        Schema::create('angle_strategy', function (Blueprint $table) {
            $table->foreignId('reach_angle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('strategy_id')->constrained()->cascadeOnDelete();
            $table->timestamp('linked_at')->useCurrent();
            $table->primary(['reach_angle_id', 'strategy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angle_strategy');
        Schema::dropIfExists('angle_lead');
    }
};
