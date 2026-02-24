<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentMaintenanceLog;
use App\Models\EquipmentMaintenanceWarning;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EquipmentMaintenanceController extends Controller
{
    /**
     * Display all maintenance warnings
     */
    public function warnings(Request $request)
    {
        $query = EquipmentMaintenanceWarning::with(['equipment', 'user', 'acknowledgedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warning type
        if ($request->filled('type')) {
            $query->where('warning_type', $request->type);
        }

        $warnings = $query->paginate(15);

        return view('client.equipment.maintenance.warnings', compact('warnings'));
    }

    /**
     * Display maintenance logs
     */
    public function logs(Request $request)
    {
        $query = EquipmentMaintenanceLog::with(['equipment', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by equipment
        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filter by action type
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('maintenance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('maintenance_date', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $equipment = Equipment::orderBy('article')->get();

        return view('client.equipment.maintenance.logs', compact('logs', 'equipment'));
    }

    /**
     * UPDATED: Process maintenance action with AI-POWERED re-prediction
     */
    public function processMaintenance(Request $request, $warningId)
    {
        $warning = EquipmentMaintenanceWarning::with('equipment')->findOrFail($warningId);

        $validated = $request->validate([
            'action_taken' => 'required|string|max:1000',
            'condition_after' => 'required|in:Serviceable,Unserviceable',
        ]);

        try {
            DB::beginTransaction();

            $equipment = $warning->equipment;
            $conditionBefore = $equipment->condition;

            // Create maintenance log
            EquipmentMaintenanceLog::create([
                'equipment_id' => $equipment->id,
                'user_id' => auth()->id(),
                'action_type' => 'maintenance_check',
                'action_taken' => $validated['action_taken'],
                'condition_before' => $conditionBefore,
                'condition_after' => $validated['condition_after'],
                'maintenance_date' => Carbon::today(),
                'notes' => 'Maintenance schedule predicted by AI',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // UPDATED: Use AI to predict next maintenance schedule
            $prediction = $equipment->repredictMaintenanceAfterAction(
                $validated['action_taken'],
                $validated['condition_after']
            );
            
            // Update the condition
            $equipment->condition = $validated['condition_after'];
            $equipment->save();

            // Mark warning as resolved
            $warning->update([
                'status' => 'resolved',
                'acknowledged_at' => now(),
                'acknowledged_by' => auth()->id(),
                'acknowledgment_note' => $validated['action_taken']
            ]);

            DB::commit();

            $nextDate = Carbon::today()->addDays($prediction['days'])->format('M d, Y');
            $confidence = ucfirst($prediction['confidence']);
            
            return redirect()->route('client.equipment.maintenance.warnings')
                ->with('success', "✅ Maintenance recorded! AI predicted next check: {$nextDate} ({$prediction['days']} days) - Confidence: {$confidence}");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Maintenance processing error: ' . $e->getMessage());
            return back()->with('error', 'Error processing maintenance: ' . $e->getMessage());
        }
    }

    /**
     * Get maintenance warnings for dashboard
     */
    public function getDashboardWarnings()
    {
        $warnings = EquipmentMaintenanceWarning::with('equipment')
            ->active()
            ->orderBy('warning_date', 'asc')
            ->limit(5)
            ->get();

        return response()->json($warnings);
    }

    /**
     * Check and generate maintenance warnings
     */
    public function checkAndGenerateWarnings()
    {
        $today = Carbon::today();
        $warningsCreated = 0;
        $warningsUpdated = 0;
        
        // Get ALL equipment with maintenance schedules
        $equipmentList = Equipment::whereNotNull('maintenance_schedule_end')->get();

        foreach ($equipmentList as $equipment) {
            $maintenanceDate = Carbon::parse($equipment->maintenance_schedule_end);
            $daysUntil = $today->diffInDays($maintenanceDate, false);

            $warningType = null;

            // Determine warning type
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
                // Check if ACTIVE warning already exists for this equipment
                $existingWarning = EquipmentMaintenanceWarning::where('equipment_id', $equipment->id)
                    ->where('status', 'active')
                    ->first();

                if (!$existingWarning) {
                    // Determine responsible user
                    $responsibleUser = null;
                    if ($equipment->responsible_person) {
                        $responsibleUser = User::where('name', $equipment->responsible_person)->first();
                    }

                    // Create new warning
                    EquipmentMaintenanceWarning::create([
                        'equipment_id' => $equipment->id,
                        'user_id' => $responsibleUser ? $responsibleUser->id : null,
                        'warning_type' => $warningType,
                        'warning_date' => $today,
                        'status' => 'active'
                    ]);

                    $warningsCreated++;
                } else {
                    // Update warning type if it changed
                    if ($existingWarning->warning_type !== $warningType) {
                        $existingWarning->update([
                            'warning_type' => $warningType,
                            'warning_date' => $today
                        ]);
                        $warningsUpdated++;
                    }
                }

                // Update equipment maintenance status
                $equipment->updateMaintenanceStatus();
            }
        }

        return redirect()->back()->with('success', 
            "🤖 AI Warnings Generated! Created: {$warningsCreated}, Updated: {$warningsUpdated}."
        );
    }

    /**
     * NEW: Trigger AI re-prediction for specific equipment
     */
    public function repredictMaintenance($equipmentId)
    {
        try {
            $equipment = Equipment::findOrFail($equipmentId);
            $prediction = $equipment->predictMaintenanceScheduleWithAI();
            
            return redirect()->back()->with('success', 
                "🤖 AI Prediction Updated! Next maintenance in {$prediction['days']} days. Reason: {$prediction['reasoning']}"
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * NEW: Show AI prediction details
     */
    public function showPredictionDetails($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);
        
        return response()->json([
            'predicted_days' => $equipment->maintenance_prediction_days,
            'reasoning' => $equipment->maintenance_prediction_reasoning,
            'confidence' => $equipment->maintenance_prediction_confidence,
            'next_maintenance' => $equipment->maintenance_schedule_end?->format('M d, Y'),
            'days_until' => $equipment->days_until_maintenance
        ]);
    }

    /**
     * Export maintenance logs to Excel
     */
    public function exportLogs(Request $request)
    {
        // Implementation for export
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}