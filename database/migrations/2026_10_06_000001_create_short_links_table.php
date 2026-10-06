<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('section');
            $table->string('code');                 // drtakaful.com/go/{code}
            $table->string('title');
            $table->string('target')->nullable();   // page the code points at, for reference only
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'code']);
            $table->index(['user_id', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
