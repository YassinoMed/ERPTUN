<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustrialMaintenanceOrder extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'work_center_id',
        'industrial_resource_id',
        'assigned_to',
        'reference',
        'type',
        'status',
        'planned_date',
        'completed_date',
        'downtime_minutes',
        'cost',
        'checklist',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'completed_date' => 'date',
    ];

    public function workCenter()
    {
        return $this->belongsTo(ProductionWorkCenter::class, 'work_center_id');
    }

    public function resource()
    {
        return $this->belongsTo(IndustrialResource::class, 'industrial_resource_id');
    }

    public function assignee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}
