<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->enum('source', [
                'referral', 'social_media', 'cold_outreach',
                'event', 'walk_in', 'other'
            ])->default('other');
            $table->string('interest_area')->nullable();
            $table->enum('temperature', ['hot', 'warm'])->default('warm');
            $table->enum('stage', [
                'new', 'contacted', 'presented', 'negotiating', 'stalled'
            ])->default('new');
            $table->date('next_contact')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
