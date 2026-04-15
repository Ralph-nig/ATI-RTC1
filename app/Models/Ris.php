<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ris extends Model
{
    protected $table = 'ris_requests';

    protected $fillable = [
        'reference',
        'purpose',
        'date_needed',
        'notes',
        'status',           // pending | approved | rejected
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'date_needed' => 'date',
        'approved_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplies(): BelongsToMany
    {
        return $this->belongsToMany(Supplies::class, 'ris_supplies', 'ris_id', 'supply_id')
            ->withPivot(['quantity_requested', 'quantity_issued', 'status', 'issued_at'])
            ->withTimestamps();
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function generateReference(): string
    {
        $now   = now();
        $year  = $now->format('Y');
        $month = $now->format('m');

        $count = static::whereYear('created_at', $year)
                       ->whereMonth('created_at', $month)
                       ->count();

        $seq = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "RIS-{$year}-{$month}-{$seq}";
    }

    public function hasAvailableStock(): bool
    {
        foreach ($this->supplies as $supply) {
            if ($supply->quantity < $supply->pivot->quantity_requested) {
                return false;
            }
        }
        return true;
    }
}