<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment', 'disposal_method')) {
                $table->enum('disposal_method', ['Sale', 'Transfer', 'Destruction', 'Others'])
                      ->nullable()
                      ->after('condition')
                      ->comment('Disposal method for unserviceable equipment');
            }

            if (!Schema::hasColumn('equipment', 'disposal_details')) {
                $table->string('disposal_details', 255)
                      ->nullable()
                      ->after('disposal_method')
                      ->comment('Additional details when disposal method is "Others"');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(
                array_filter(
                    ['disposal_method', 'disposal_details'],
                    fn($col) => Schema::hasColumn('equipment', $col)
                )
            );
        });
    }
};