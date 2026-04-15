<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main RIS request table
        Schema::create('ris_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('purpose');
            $table->date('date_needed')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Pivot: which supplies are in each RIS
        Schema::create('ris_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ris_id')->constrained('ris_requests')->cascadeOnDelete();
            $table->foreignId('supply_id')->constrained('supplies')->cascadeOnDelete();
            $table->unsignedInteger('quantity_requested');
            $table->unsignedInteger('quantity_issued')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ris_supplies');
        Schema::dropIfExists('ris_requests');
    }
};