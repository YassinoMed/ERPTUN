<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionShopfloorEvent extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'production_work_center_id',
        'employee_id',
        'event_type',
        'status',
        'quantity',
        'downtime_minutes',
        'happened_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'happened_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function workCenter()
    {
        return $this->belongsTo(ProductionWorkCenter::class, 'production_work_center_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
