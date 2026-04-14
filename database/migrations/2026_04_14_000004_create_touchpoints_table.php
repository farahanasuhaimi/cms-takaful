<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('touchpoints', function (Blueprint $table) {
            $table->id();
            $table->morphs('touchable');
            $table->dateTime('contacted_at');
            $table->enum('channel', [
                'whatsapp', 'phone_call', 'in_person',
                'dm_instagram', 'dm_facebook', 'email', 'other'
            ])->default('whatsapp');
            $table->string('topic');
            $table->text('notes')->nullable();
            $table->string('next_action')->nullable();
            $table->date('next_action_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('touchpoints');
    }
};
