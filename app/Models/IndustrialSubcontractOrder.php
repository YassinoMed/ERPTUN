<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustrialSubcontractOrder extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'production_routing_step_id',
        'vender_id',
        'reference',
        'status',
        'quantity',
        'unit_cost',
        'planned_send_date',
        'planned_receive_date',
        'actual_receive_date',
        'quality_notes',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'planned_send_date' => 'date',
        'planned_receive_date' => 'date',
        'actual_receive_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function step()
    {
        return $this->belongsTo(ProductionRoutingStep::class, 'production_routing_step_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vender::class, 'vender_id');
    }
}
