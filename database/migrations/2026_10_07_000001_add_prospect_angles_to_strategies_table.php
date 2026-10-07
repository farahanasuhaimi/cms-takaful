<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A strategy suits a prospect type (cold / warm / hot) when it has an
     * angle for it — the angle text is the tag, so the two can't disagree.
     */
    public function up(): void
    {
        Schema::table('strategies', function (Blueprint $table) {
            // Keys match DashboardInsightService::LINES, plus 'general'.
            $table->string('product_line', 30)->nullable()->after('audience');
            $table->text('angle_cold')->nullable()->after('content');
            $table->text('angle_warm')->nullable()->after('angle_cold');
            $table->text('angle_hot')->nullable()->after('angle_warm');
            $table->text('key_facts')->nullable()->after('angle_hot');
        });
    }

    public function down(): void
    {
        Schema::table('strategies', function (Blueprint $table) {
            $table->dropColumn(['product_line', 'angle_cold', 'angle_warm', 'angle_hot', 'key_facts']);
        });
    }
};
