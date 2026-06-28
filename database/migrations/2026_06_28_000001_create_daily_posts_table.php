<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('post_date');
            $table->string('platform'); // instagram, facebook, whatsapp, tiktok
            $table->string('topic');
            $table->text('caption')->nullable();
            $table->text('image_prompt')->nullable();
            $table->string('status')->default('draft'); // draft, ready, posted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_posts');
    }
};
