<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OkrKeyResult extends Model
{
    protected $fillable = [
        'okr_objective_id',
        'metric_name',
        'start_value',
        'target_value',
        'current_value',
        'unit',
        'status',
        'due_date',
        'created_by',
    ];

    public static $statuses = [
        'on_track' => 'On Track',
        'needs_attention' => 'Needs Attention',
        'at_risk' => 'At Risk',
        'completed' => 'Completed',
    ];

    public function objective()
    {
        return $this->belongsTo(OkrObjective::class, 'okr_objective_id');
    }
}
