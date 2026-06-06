<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_focus_point', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('focus_point_id');
            $table->primary(['lead_id', 'focus_point_id']);
            $table->foreign('lead_id')->references('id')->on('leads')->cascadeOnDelete();
            $table->foreign('focus_point_id')->references('id')->on('focus_points')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_focus_point');
    }
};
