<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\OrgChartHead
 *
 * Stores the section head assignment for each unit slot in the org chart.
 * The `unit_key` column is UNIQUE, enforcing a maximum of one head per slot.
 *
 * @property int         $id
 * @property string      $unit_key   e.g. 'pme', 'admin', 'ocd_director' …
 * @property int         $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class OrgChartHead extends Model
{
    protected $table = 'org_chart_heads';

    protected $fillable = ['unit_key', 'user_id'];

    /**
     * The user assigned as head of this section.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}