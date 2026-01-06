<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipment;
use App\Models\EquipmentMaintenanceWarning;
use Carbon\Carbon;

class GenerateMaintenanceWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:generate-warnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate maintenance warnings for equipment that needs maintenance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting maintenance warnings generation...');
        
        $today = Carbon::today();
        $warningsCreated = 0;
        $warningsUpdated = 0;
        
        // Get equipment that has maintenance schedules
        $equipmentList = Equipment::whereNotNull('maintenance_schedule_end')
            ->where('maintenance_status', '!=', 'completed')
            ->get();

        $this->info("Found {$equipmentList->count()} equipment items to check.");

        foreach ($equipmentList as $equipment) {
            $maintenanceDate = Carbon::parse($equipment->maintenance_schedule_end);
            $daysUntil = $today->diffInDays($maintenanceDate, false);

            $warningType = null;

            if ($daysUntil < 0) {
                // Overdue
                if ($daysUntil < -30) {
                    $warningType = 'critical';
                } else {
                    $warningType = 'overdue';
                }
            } elseif ($daysUntil <= 7) {
                // Due soon (within 7 days)
                $warningType = 'due_soon';
            }

            if ($warningType) {
                // Check if warning already exists for today
                $existingWarning = EquipmentMaintenanceWarning::where('equipment_id', $equipment->id)
                    ->where('warning_date', $today)
                    ->where('status', 'active')
                    ->first();

                if (!$existingWarning) {
                    // Determine responsible user
                    $responsibleUser = null;
                    if ($equipment->responsible_person) {
                        $responsibleUser = \App\Models\User::where('name', $equipment->responsible_person)->first();
                    }

                    EquipmentMaintenanceWarning::create([
                        'equipment_id' => $equipment->id,
                        'user_id' => $responsibleUser ? $responsibleUser->id : null,
                        'warning_type' => $warningType,
                        'warning_date' => $today,
                        'status' => 'active'
                    ]);

                    $warningsCreated++;
                    $this->line("✓ Created {$warningType} warning for: {$equipment->article}");
                } else {
                    // Update existing warning type if it has changed
                    if ($existingWarning->warning_type !== $warningType) {
                        $existingWarning->update(['warning_type' => $warningType]);
                        $warningsUpdated++;
                        $this->line("↻ Updated warning type to {$warningType} for: {$equipment->article}");
                    }
                }

                // Update equipment maintenance status
                $equipment->updateMaintenanceStatus();
            }
        }

        $this->newLine();
        $this->info("✓ Maintenance warnings generation completed!");
        $this->info("  - New warnings created: {$warningsCreated}");
        $this->info("  - Existing warnings updated: {$warningsUpdated}");

        return 0;
    }
}

// To register this command, add it to app/Console/Kernel.php:
/*
protected function schedule(Schedule $schedule)
{
    // Run daily at 8:00 AM
    $schedule->command('maintenance:generate-warnings')->dailyAt('08:00');
    
    // Or run every hour
    // $schedule->command('maintenance:generate-warnings')->hourly();
}
*/