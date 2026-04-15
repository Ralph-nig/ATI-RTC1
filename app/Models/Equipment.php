<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'property_number',
        'document_type',
        'document_number',
        'article',
        'classification',
        'description',
        'unit_of_measurement',
        'unit_value',
        'quantity',
        'condition',
        'disposal_method',
        'disposal_details',
        'acquisition_date',
        'location',
        'responsibility_center',
        'responsible_person',
        'remarks',
        'user_id'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'unit_value' => 'decimal:2'
    ];

    /**
     * Get the user who created this equipment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Departments available as Responsibility Centers */
    const DEPARTMENTS = ['ISS', 'AFU', 'CDMS', 'PAS', 'PMEU', 'OCD', 'DORM'];

    /**
     * Generate next document number for the given type and month.
     * Format: {TYPE}-{YYYY}-{MM}-{NNNN}  e.g. ICS-2026-04-0001
     * Counter resets every month.
     */
    public static function generateDocumentNumber(string $type): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $prefix = "{$type}-{$year}-{$month}-";

        $last = self::where('document_type', $type)
            ->where('document_number', 'like', "{$prefix}%")
            ->orderByDesc('document_number')
            ->value('document_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Suggest document type based on unit value.
     * ICS  → below ₱50,000
     * PAR  → ₱50,000 and above
     */
    public static function suggestDocumentType(float $unitValue): string
    {
        return $unitValue < 50000 ? 'ICS' : 'PAR';
    }


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
            return $query->where(function ($q) use ($search) {
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
}