<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angle_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reach_angle_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->timestamp('reached_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angle_client');
    }
};
