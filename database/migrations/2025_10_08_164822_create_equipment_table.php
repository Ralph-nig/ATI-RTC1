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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->enum('document_type', ['ICS', 'PAR'])->nullable();
            $table->string('document_number', 50)->nullable()->unique();
            $table->string('article');
            $table->string('classification')->nullable();
            $table->text('description')->nullable();
            $table->string('property_number')->unique(); // Format: YYYY-MM-DD-ID
            $table->string('unit_of_measurement');
            $table->decimal('unit_value', 10, 2);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->enum('condition', ['Serviceable', 'Unserviceable'])->default('Serviceable');
            $table->enum('disposal_method', ['Sale', 'Transfer', 'Destruction', 'Others'])->nullable();
            $table->string('disposal_details')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('location')->nullable();
            $table->string('responsibility_center', 20)->nullable();
            $table->string('responsible_person')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};