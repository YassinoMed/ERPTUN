<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustrialQualityPlan extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'production_routing_id',
        'name',
        'check_stage',
        'sampling_rule',
        'status',
        'acceptance_criteria',
        'notes',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(ProductService::class, 'product_id');
    }

    public function routing()
    {
        return $this->belongsTo(ProductionRouting::class, 'production_routing_id');
    }
}
