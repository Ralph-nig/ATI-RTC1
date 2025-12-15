<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeletedEquipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'property_number',
        'article',
        'classification',
        'description',
        'unit_of_measurement',
        'unit_value',
        'condition',
        'acquisition_date',
        'location',
        'responsible_person',
        'remarks',
        'reason',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'unit_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('article', 'like', "%{$term}%")
              ->orWhere('classification', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('property_number', 'like', "%{$term}%");
        });
    }
}