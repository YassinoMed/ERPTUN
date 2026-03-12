<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgriPurchaseContract extends HedgingModel
{
    use HasFactory;

    protected $fillable = [
        'cooperative_id',
        'contract_number',
        'buyer_name',
        'crop_type',
        'quantity',
        'unit',
        'fixed_price',
        'price_currency',
        'delivery_start',
        'delivery_end',
        'status',
        'hedge_ratio',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'fixed_price' => 'decimal:2',
        'hedge_ratio' => 'decimal:2',
        'delivery_start' => 'date',
        'delivery_end' => 'date',
    ];

    public function cooperative()
    {
        return $this->belongsTo(AgriCooperative::class, 'cooperative_id');
    }

    public function hedgePositions()
    {
        return $this->hasMany(AgriHedgePosition::class, 'contract_id');
    }
}
