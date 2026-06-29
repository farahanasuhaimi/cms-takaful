<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_posts', function (Blueprint $table) {
            $table->foreignId('reach_angle_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('daily_posts', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ReachAngle::class);
            $table->dropColumn('reach_angle_id');
        });
    }
};
