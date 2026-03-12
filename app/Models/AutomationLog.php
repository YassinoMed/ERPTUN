<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationLog extends Model
{
    protected $fillable = [
        'automation_rule_id',
        'created_by',
        'event_name',
        'model_type',
        'model_id',
        'payload',
        'status',
        'result',
        'triggered_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'triggered_at' => 'datetime',
    ];

    public function automationRule()
    {
        return $this->belongsTo(AutomationRule::class, 'automation_rule_id');
    }
}
