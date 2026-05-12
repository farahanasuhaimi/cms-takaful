<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('age')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
        });

        Schema::create('quotation_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('plan_name');
            $table->string('type')->nullable();
            $table->string('coverage')->nullable();
            $table->string('umur_matang')->nullable();
            $table->string('pampasan_matang')->nullable();
            $table->string('kenaikan')->nullable();
            $table->string('plan_type')->nullable();
            $table->text('privilege')->nullable();
            $table->string('waiver')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
        });

        Schema::create('quotation_premiums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_person_id')->constrained('quotation_people')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_premiums');
        Schema::dropIfExists('quotation_plans');
        Schema::dropIfExists('quotation_people');
        Schema::dropIfExists('quotations');
    }
};
