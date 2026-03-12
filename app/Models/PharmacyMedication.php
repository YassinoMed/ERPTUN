<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyMedication extends Model
{
    protected $fillable = [
        'product_service_id',
        'name',
        'sku',
        'dosage_form',
        'strength',
        'lot_number',
        'expiry_date',
        'stock_quantity',
        'reorder_level',
        'unit_price',
        'created_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'stock_quantity' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function productService()
    {
        return $this->belongsTo(ProductService::class, 'product_service_id');
    }
}
