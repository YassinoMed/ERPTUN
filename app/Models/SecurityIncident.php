<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityIncident extends Model
{
    protected $fillable = [
        'title',
        'incident_reference',
        'incident_type',
        'severity',
        'status',
        'affected_module',
        'reported_by',
        'owner_id',
        'detected_at',
        'summary',
        'containment_actions',
        'resolution_notes',
        'created_by',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
    ];

    public static $statuses = [
        'open' => 'Open',
        'investigating' => 'Investigating',
        'contained' => 'Contained',
        'closed' => 'Closed',
    ];

    public static $severities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
