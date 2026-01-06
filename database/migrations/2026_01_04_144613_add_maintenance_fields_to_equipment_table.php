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
        // Add maintenance schedule fields to equipment table
        Schema::table('equipment', function (Blueprint $table) {
            $table->date('maintenance_schedule_start')->nullable()->after('acquisition_date');
            $table->date('maintenance_schedule_end')->nullable()->after('maintenance_schedule_start');
            $table->enum('maintenance_status', ['pending', 'due', 'overdue', 'completed'])->default('pending')->after('maintenance_schedule_end');
            $table->timestamp('last_maintenance_check')->nullable()->after('maintenance_status');
        });

        // Create equipment_maintenance_logs table
        Schema::create('equipment_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['maintenance_check', 'status_update', 'warning_acknowledged'])->default('maintenance_check');
            $table->text('action_taken')->nullable();
            $table->enum('condition_before', ['Serviceable', 'Unserviceable'])->nullable();
            $table->enum('condition_after', ['Serviceable', 'Unserviceable'])->nullable();
            $table->date('maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('equipment_id');
            $table->index('user_id');
            $table->index('action_type');
            $table->index('maintenance_date');
        });

        // Create equipment_maintenance_warnings table
        Schema::create('equipment_maintenance_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('warning_type', ['due_soon', 'overdue', 'critical'])->default('due_soon');
            $table->date('warning_date');
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active');
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('acknowledgment_note')->nullable();
            $table->timestamps();
            
            $table->index('equipment_id');
            $table->index('warning_type');
            $table->index('status');
            $table->index('warning_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance_warnings');
        Schema::dropIfExists('equipment_maintenance_logs');
        
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_schedule_start',
                'maintenance_schedule_end',
                'maintenance_status',
                'last_maintenance_check'
            ]);
        });
    }
};