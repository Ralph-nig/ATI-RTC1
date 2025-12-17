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
            // Add disposal_method column after condition
            $table->enum('disposal_method', ['Sale', 'Transfer', 'Destruction', 'Others'])
                  ->nullable()
                  ->after('condition')
                  ->comment('Disposal method for unserviceable equipment');
            
            // Add disposal_details column after disposal_method
            $table->string('disposal_details', 255)
                  ->nullable()
                  ->after('disposal_method')
                  ->comment('Additional details when disposal method is "Others"');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['disposal_method', 'disposal_details']);
        });
    }
};