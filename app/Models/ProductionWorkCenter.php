<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionWorkCenter extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'industrial_resource_id',
        'machine_code',
        'cost_per_hour',
        'capacity_hours_per_day',
        'capacity_workers',
        'is_bottleneck',
        'created_by',
    ];

    protected $casts = [
        'is_bottleneck' => 'boolean',
    ];

    public function resource()
    {
        return $this->belongsTo(IndustrialResource::class, 'industrial_resource_id');
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'work_center_id');
    }

    public function operations()
    {
        return $this->hasMany(ProductionOrderOperation::class, 'work_center_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(ProductionTimeLog::class, 'work_center_id');
    }

    public function maintenanceOrders()
    {
        return $this->hasMany(IndustrialMaintenanceOrder::class, 'work_center_id')->latest();
    }

    public function shopfloorEvents()
    {
        return $this->hasMany(ProductionShopfloorEvent::class, 'production_work_center_id')->latest('happened_at');
    }
}
