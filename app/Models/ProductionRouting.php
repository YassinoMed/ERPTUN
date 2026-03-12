<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionRouting extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'name',
        'status',
        'notes',
        'created_by',
    ];

    public function product()
    {
        return $this->hasOne(ProductService::class, 'id', 'product_id');
    }

    public function steps()
    {
        return $this->hasMany(ProductionRoutingStep::class, 'production_routing_id')->orderBy('sequence');
    }

    public function orders()
    {
        return $this->hasMany(ProductionOrder::class, 'production_routing_id');
    }
}
