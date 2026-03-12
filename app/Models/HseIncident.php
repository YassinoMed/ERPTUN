<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HseIncident extends Model
{
    protected $fillable = [
        'incident_code',
        'title',
        'category',
        'severity',
        'status',
        'occurred_on',
        'location',
        'reported_by_employee_id',
        'actions',
        'notes',
        'created_by',
    ];

    public static $severities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public static $statuses = [
        'open' => 'Open',
        'investigating' => 'Investigating',
        'closed' => 'Closed',
    ];

    public function reporter()
    {
        return $this->belongsTo(Employee::class, 'reported_by_employee_id');
    }
}
