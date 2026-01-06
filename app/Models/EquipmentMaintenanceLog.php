<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Equipment Maintenance Log Model
 * Tracks all maintenance actions and checks
 */
class EquipmentMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'action_type',
        'action_taken',
        'condition_before',
        'condition_after',
        'maintenance_date',
        'notes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the equipment that owns this log
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by action type
     */
    public function scopeByActionType($query, $type)
    {
        return $query->where('action_type', $type);
    }

    /**
     * Scope for filtering by equipment
     */
    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }

    /**
     * Scope for maintenance checks only
     */
    public function scopeMaintenanceChecks($query)
    {
        return $query->where('action_type', 'maintenance_check');
    }

    /**
     * Scope for status updates only
     */
    public function scopeStatusUpdates($query)
    {
        return $query->where('action_type', 'status_update');
    }

    /**
     * Get formatted action type
     */
    public function getFormattedActionTypeAttribute()
    {
        $types = [
            'maintenance_check' => 'Maintenance Check',
            'status_update' => 'Status Update',
            'warning_acknowledged' => 'Warning Acknowledged'
        ];

        return $types[$this->action_type] ?? $this->action_type;
    }

    /**
     * Check if condition changed
     */
    public function hasConditionChange()
    {
        return $this->condition_before !== $this->condition_after;
    }

    /**
     * Get condition change description
     */
    public function getConditionChangeAttribute()
    {
        if (!$this->hasConditionChange()) {
            return 'No change';
        }

        return "{$this->condition_before} → {$this->condition_after}";
    }
}