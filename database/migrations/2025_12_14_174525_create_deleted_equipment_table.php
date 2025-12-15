<?php
// ============================================
// 1. MIGRATION - Create deleted_equipment table
// File: database/migrations/2025_01_15_create_deleted_equipment_table.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('equipment_id'); // Original ID
            $table->string('property_number');
            $table->string('article');
            $table->string('classification')->nullable();
            $table->text('description')->nullable();
            $table->string('unit_of_measurement');
            $table->decimal('unit_value', 10, 2);
            $table->enum('condition', ['Serviceable', 'Unserviceable']);
            $table->date('acquisition_date')->nullable();
            $table->string('location')->nullable();
            $table->string('responsible_person')->nullable();
            $table->text('remarks')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_equipment');
    }
};
