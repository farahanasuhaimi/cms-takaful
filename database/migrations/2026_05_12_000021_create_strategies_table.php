<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['prospecting', 'content', 'objection_handling', 'follow_up', 'referral', 'closing']);
            $table->enum('channel', ['whatsapp', 'instagram', 'facebook', 'face_to_face', 'general'])->default('general');
            $table->enum('audience', ['strangers', 'warm_leads', 'family_friends', 'corporate', 'general'])->default('general');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('type', ['script', 'flow'])->default('script');
            $table->enum('source', ['provided', 'ai_guided', 'self_made'])->default('self_made');
            $table->text('content')->nullable();
            $table->enum('status', ['draft', 'active', 'removed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategies');
    }
};
