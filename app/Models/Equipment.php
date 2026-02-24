<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Services\EquipmentMaintenancePredictionService;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'property_number',
        'article',
        'classification',
        'description',
        'unit_of_measurement',
        'unit_value',
        'condition',
        'disposal_method',
        'disposal_details',
        'acquisition_date',
        'maintenance_schedule_start',
        'maintenance_schedule_end',
        'maintenance_status',
        'maintenance_prediction_days',
        'maintenance_prediction_reasoning',
        'maintenance_prediction_confidence',
        'last_maintenance_check',
        'location',
        'responsible_person',
        'remarks',
        'user_id'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'maintenance_schedule_start' => 'date',
        'maintenance_schedule_end' => 'date',
        'last_maintenance_check' => 'datetime',
        'unit_value' => 'decimal:2'
    ];

    /**
     * Get the user who created this equipment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get maintenance logs for this equipment
     */
    public function maintenanceLogs()
    {
        return $this->hasMany(EquipmentMaintenanceLog::class);
    }

    /**
     * Get maintenance warnings for this equipment
     */
    public function maintenanceWarnings()
    {
        return $this->hasMany(EquipmentMaintenanceWarning::class);
    }

    /**
     * Get active maintenance warnings
     */
    public function activeWarnings()
    {
        return $this->maintenanceWarnings()->active();
    }

    /**
     * Generate property number in format YYYY-MM-DD-ID
     */
    public static function generatePropertyNumber()
    {
        $date = Carbon::now()->format('Y-m-d');
        $lastEquipment = self::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();
        
        $nextId = $lastEquipment ? $lastEquipment->id + 1 : 1;
        
        return $date . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted acquisition date
     */
    public function getFormattedAcquisitionDateAttribute()
    {
        return $this->acquisition_date ? $this->acquisition_date->format('M d, Y') : 'N/A';
    }

    /**
     * Check if equipment is serviceable
     */
    public function isServiceable()
    {
        return $this->condition === 'Serviceable';
    }

    /**
     * Get condition badge class
     */
    public function getConditionBadgeClassAttribute()
    {
        return $this->condition === 'Serviceable' ? 'status-serviceable' : 'status-unserviceable';
    }

    /**
     * Get formatted disposal method
     */
    public function getFormattedDisposalMethodAttribute()
    {
        if (!$this->disposal_method) {
            return 'N/A';
        }
        
        if ($this->disposal_method === 'Others' && $this->disposal_details) {
            return 'Others: ' . $this->disposal_details;
        }
        
        return $this->disposal_method;
    }

    /**
     * Check if maintenance is due (within 7 days)
     */
    public function isMaintenanceDue()
    {
        if (!$this->maintenance_schedule_end) {
            return false;
        }

        $today = Carbon::today();
        $daysUntilDue = $today->diffInDays($this->maintenance_schedule_end, false);

        return $daysUntilDue <= 7 && $daysUntilDue >= 0;
    }

    /**
     * Check if maintenance is overdue
     */
    public function isMaintenanceOverdue()
    {
        if (!$this->maintenance_schedule_end) {
            return false;
        }

        return Carbon::today()->isAfter($this->maintenance_schedule_end);
    }

    /**
     * Get days until maintenance is due (negative if overdue)
     */
    public function getDaysUntilMaintenanceAttribute()
    {
        if (!$this->maintenance_schedule_end) {
            return null;
        }

        return Carbon::today()->diffInDays($this->maintenance_schedule_end, false);
    }

    /**
     * Get maintenance status badge information
     */
    public function getMaintenanceStatusBadgeAttribute()
    {
        if ($this->isMaintenanceOverdue()) {
            return [
                'text' => 'Overdue',
                'class' => 'danger',
                'icon' => 'exclamation-triangle',
                'color' => '#dc3545'
            ];
        }

        if ($this->isMaintenanceDue()) {
            return [
                'text' => 'Due Soon',
                'class' => 'warning',
                'icon' => 'clock',
                'color' => '#ffc107'
            ];
        }

        if ($this->maintenance_schedule_end) {
            return [
                'text' => 'Scheduled',
                'class' => 'info',
                'icon' => 'calendar-check',
                'color' => '#17a2b8'
            ];
        }

        return [
            'text' => 'No Schedule',
            'class' => 'secondary',
            'icon' => 'calendar-times',
            'color' => '#6c757d'
        ];
    }

    /**
     * Update maintenance status automatically
     */
    public function updateMaintenanceStatus()
    {
        if (!$this->maintenance_schedule_end) {
            $this->maintenance_status = 'pending';
        } elseif ($this->isMaintenanceOverdue()) {
            $this->maintenance_status = 'overdue';
        } elseif ($this->isMaintenanceDue()) {
            $this->maintenance_status = 'due';
        } else {
            $this->maintenance_status = 'pending';
        }

        $this->save();
    }

    /**
     * NEW: AI-Powered maintenance schedule prediction
     */
    public function predictMaintenanceScheduleWithAI()
    {
        $service = new EquipmentMaintenancePredictionService();
        $prediction = $service->predictMaintenanceSchedule($this);
        
        $today = Carbon::today();
        
        $this->maintenance_schedule_start = $today;
        $this->maintenance_schedule_end = $today->copy()->addDays($prediction['days']);
        $this->maintenance_status = 'pending';
        $this->maintenance_prediction_days = $prediction['days'];
        $this->maintenance_prediction_reasoning = $prediction['reasoning'];
        $this->maintenance_prediction_confidence = $prediction['confidence'];
        
        $this->save();
        
        return $prediction;
    }

    /**
     * NEW: Re-predict after maintenance action
     */
    public function repredictMaintenanceAfterAction(string $actionTaken, string $conditionAfter)
    {
        $service = new EquipmentMaintenancePredictionService();
        $prediction = $service->repredictAfterMaintenance($this, $actionTaken, $conditionAfter);
        
        $today = Carbon::today();
        
        $this->maintenance_schedule_start = $today;
        $this->maintenance_schedule_end = $today->copy()->addDays($prediction['days']);
        $this->maintenance_status = 'pending';
        $this->maintenance_prediction_days = $prediction['days'];
        $this->maintenance_prediction_reasoning = $prediction['reasoning'];
        $this->maintenance_prediction_confidence = $prediction['confidence'];
        $this->last_maintenance_check = now();
        
        $this->save();
        
        return $prediction;
    }

    /**
     * DEPRECATED: Use predictMaintenanceScheduleWithAI() instead
     */
    public function setAutoMaintenanceSchedule()
    {
        return $this->predictMaintenanceScheduleWithAI();
    }

    /**
     * DEPRECATED: Use repredictMaintenanceAfterAction() instead
     */
    public function rescheduleMaintenanceFor30Days()
    {
        // Fallback to 30 days if AI fails
        $today = Carbon::today();
        
        $this->maintenance_schedule_start = $today;
        $this->maintenance_schedule_end = $today->copy()->addDays(30);
        $this->maintenance_status = 'pending';
        $this->last_maintenance_check = now();
        
        $this->save();
    }

    /**
     * Check if equipment needs maintenance warning
     */
    public function needsMaintenanceWarning()
    {
        if (!$this->maintenance_schedule_end) {
            return false;
        }

        return $this->isMaintenanceDue() || $this->isMaintenanceOverdue();
    }

    /**
     * Scope for filtering by condition
     */
    public function scopeByCondition($query, $condition)
    {
        if ($condition) {
            return $query->where('condition', $condition);
        }
        return $query;
    }

    /**
     * Scope for searching
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('article', 'like', "%{$search}%")
                  ->orWhere('classification', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('property_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('responsible_person', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope for equipment with maintenance due
     */
    public function scopeMaintenanceDue($query)
    {
        return $query->whereNotNull('maintenance_schedule_end')
                    ->whereDate('maintenance_schedule_end', '>=', Carbon::today())
                    ->whereDate('maintenance_schedule_end', '<=', Carbon::today()->addDays(7));
    }

    /**
     * Scope for equipment with overdue maintenance
     */
    public function scopeMaintenanceOverdue($query)
    {
        return $query->whereNotNull('maintenance_schedule_end')
                    ->whereDate('maintenance_schedule_end', '<', Carbon::today());
    }

    /**
     * Scope for equipment requiring maintenance attention
     */
    public function scopeRequiringMaintenance($query)
    {
        return $query->whereNotNull('maintenance_schedule_end')
                    ->where(function($q) {
                        $q->whereDate('maintenance_schedule_end', '<=', Carbon::today()->addDays(7));
                    });
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // UPDATED: Use AI prediction when creating equipment
        static::created(function ($equipment) {
            try {
                $equipment->predictMaintenanceScheduleWithAI();
            } catch (\Exception $e) {
                \Log::error('AI prediction failed for new equipment: ' . $e->getMessage());
                // Fallback to 30 days
                $today = Carbon::today();
                $equipment->maintenance_schedule_start = $today;
                $equipment->maintenance_schedule_end = $today->copy()->addDays(30);
                $equipment->maintenance_status = 'pending';
                $equipment->save();
            }
        });

        // Automatically update maintenance status before saving
        static::saving(function ($equipment) {
            if ($equipment->maintenance_schedule_end && !$equipment->isDirty('maintenance_schedule_start')) {
                $today = Carbon::today();
                $maintenanceDate = Carbon::parse($equipment->maintenance_schedule_end);
                
                if ($today->isAfter($maintenanceDate)) {
                    $equipment->maintenance_status = 'overdue';
                } elseif ($today->diffInDays($maintenanceDate, false) <= 7) {
                    $equipment->maintenance_status = 'due';
                } else {
                    $equipment->maintenance_status = 'pending';
                }
            }
        });
    }
}