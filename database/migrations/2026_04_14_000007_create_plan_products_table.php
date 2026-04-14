<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_products', function (Blueprint $table) {
            $table->id();
            $table->enum('plan_type', [
                'medical', 'critical_illness', 'personal_accident',
                'group', 'hibah', 'income', 'other'
            ]);
            $table->string('name');
            $table->json('attributes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_products');
    }
};
