<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Equipment Maintenance Warning Model
 * Tracks maintenance warnings and acknowledgments
 */
class EquipmentMaintenanceWarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'warning_type',
        'warning_date',
        'status',
        'acknowledged_at',
        'acknowledged_by',
        'acknowledgment_note'
    ];

    protected $casts = [
        'warning_date' => 'date',
        'acknowledged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the equipment that owns this warning
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user this warning is assigned to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who acknowledged this warning
     */
    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope for active warnings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for acknowledged warnings
     */
    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    /**
     * Scope for resolved warnings
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope for overdue warnings
     */
    public function scopeOverdue($query)
    {
        return $query->where('warning_type', 'overdue');
    }

    /**
     * Scope for critical warnings
     */
    public function scopeCritical($query)
    {
        return $query->where('warning_type', 'critical');
    }

    /**
     * Scope for due soon warnings
     */
    public function scopeDueSoon($query)
    {
        return $query->where('warning_type', 'due_soon');
    }

    /**
     * Get formatted warning type
     */
    public function getFormattedWarningTypeAttribute()
    {
        $types = [
            'due_soon' => 'Due Soon',
            'overdue' => 'Overdue',
            'critical' => 'Critical'
        ];

        return $types[$this->warning_type] ?? $this->warning_type;
    }

    /**
     * Get badge color for warning type
     */
    public function getBadgeColorAttribute()
    {
        $colors = [
            'due_soon' => 'warning',
            'overdue' => 'danger',
            'critical' => 'dark'
        ];

        return $colors[$this->warning_type] ?? 'secondary';
    }

    /**
     * Check if warning is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if warning is resolved
     */
    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if warning is acknowledged
     */
    public function isAcknowledged()
    {
        return $this->status === 'acknowledged';
    }
}