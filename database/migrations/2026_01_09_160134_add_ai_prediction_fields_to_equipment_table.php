<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Add AI prediction tracking fields
            $table->integer('maintenance_prediction_days')->nullable()->after('maintenance_status');
            $table->text('maintenance_prediction_reasoning')->nullable()->after('maintenance_prediction_days');
            $table->enum('maintenance_prediction_confidence', ['high', 'medium', 'low'])->nullable()->after('maintenance_prediction_reasoning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_prediction_days',
                'maintenance_prediction_reasoning',
                'maintenance_prediction_confidence'
            ]);
        });
    }
};