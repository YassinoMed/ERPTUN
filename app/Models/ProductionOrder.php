<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionOrder extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'product_id',
        'production_bom_version_id',
        'production_routing_id',
        'warehouse_id',
        'work_center_id',
        'employee_id',
        'production_shift_team_id',
        'quantity_planned',
        'quantity_produced',
        'planned_machine_hours',
        'planned_labor_hours',
        'planned_start_date',
        'planned_end_date',
        'priority',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
    ];

    public function product()
    {
        return $this->hasOne(ProductService::class, 'id', 'product_id');
    }

    public function bomVersion()
    {
        return $this->hasOne(ProductionBomVersion::class, 'id', 'production_bom_version_id');
    }

    public function warehouse()
    {
        return $this->hasOne(warehouse::class, 'id', 'warehouse_id');
    }

    public function routing()
    {
        return $this->belongsTo(ProductionRouting::class, 'production_routing_id');
    }

    public function workCenter()
    {
        return $this->hasOne(ProductionWorkCenter::class, 'id', 'work_center_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function shiftTeam()
    {
        return $this->belongsTo(ProductionShiftTeam::class, 'production_shift_team_id');
    }

    public function operations()
    {
        return $this->hasMany(ProductionOrderOperation::class, 'production_order_id')->orderBy('sequence');
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterialMove::class, 'production_order_id');
    }

    public function qualityChecks()
    {
        return $this->hasMany(ProductionQualityCheck::class, 'production_order_id')->latest();
    }

    public function subcontractOrders()
    {
        return $this->hasMany(IndustrialSubcontractOrder::class, 'production_order_id')->latest();
    }

    public function costRecords()
    {
        return $this->hasMany(IndustrialCostRecord::class, 'production_order_id')->latest();
    }
}
