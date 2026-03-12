<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionRoutingStep extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'production_routing_id',
        'sequence',
        'name',
        'work_center_id',
        'industrial_resource_id',
        'planned_minutes',
        'setup_cost',
        'run_cost',
        'scrap_percent',
        'is_subcontracted',
        'instructions',
        'created_by',
    ];

    protected $casts = [
        'is_subcontracted' => 'boolean',
    ];

    public function routing()
    {
        return $this->belongsTo(ProductionRouting::class, 'production_routing_id');
    }

    public function workCenter()
    {
        return $this->belongsTo(ProductionWorkCenter::class, 'work_center_id');
    }

    public function resource()
    {
        return $this->belongsTo(IndustrialResource::class, 'industrial_resource_id');
    }
}
