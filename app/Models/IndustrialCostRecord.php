<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustrialCostRecord extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'product_id',
        'cost_type',
        'amount',
        'quantity_basis',
        'notes',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductService::class, 'product_id');
    }
}
