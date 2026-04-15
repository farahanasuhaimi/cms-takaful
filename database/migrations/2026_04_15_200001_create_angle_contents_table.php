<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angle_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angle_id')->constrained('reach_angles')->cascadeOnDelete();
            $table->unsignedInteger('batch');
            $table->enum('style', ['casual', 'story', 'factual']);
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->string('model')->default('deepseek-chat');
            $table->timestamps();

            $table->index(['angle_id', 'batch']);
            $table->index(['angle_id', 'is_pinned']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angle_contents');
    }
};
