<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('plan_type', [
                'medical', 'critical_illness', 'personal_accident',
                'group', 'hibah', 'income', 'other'
            ]);
            $table->string('plan_name')->nullable();
            $table->decimal('coverage_amount', 12, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->decimal('premium_monthly', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
