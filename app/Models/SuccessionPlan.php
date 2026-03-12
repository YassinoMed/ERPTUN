<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessionPlan extends Model
{
    protected $fillable = [
        'employee_id',
        'successor_employee_id',
        'readiness_level',
        'risk_level',
        'target_date',
        'status',
        'notes',
        'created_by',
    ];

    public static $levels = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    public static $statuses = [
        'planned' => 'Planned',
        'active' => 'Active',
        'completed' => 'Completed',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function successor()
    {
        return $this->belongsTo(Employee::class, 'successor_employee_id');
    }
}
