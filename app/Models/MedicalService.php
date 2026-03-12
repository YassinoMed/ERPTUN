<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalService extends Model
{
    protected $fillable = [
        'product_service_id',
        'code',
        'name',
        'service_type',
        'price',
        'default_coverage_rate',
        'notes',
        'created_by',
    ];

    public function productService()
    {
        return $this->belongsTo(ProductService::class, 'product_service_id');
    }
}
