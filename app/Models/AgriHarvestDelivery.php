<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriHarvestDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooperative_id',
        'member_id',
        'lot_id',
        'crop_type',
        'quantity',
        'unit',
        'delivery_date',
        'price_per_unit',
        'total_value',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'price_per_unit' => 'decimal:2',
        'total_value' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    public function cooperative()
    {
        return $this->belongsTo(AgriCooperative::class, 'cooperative_id');
    }

    public function member()
    {
        return $this->belongsTo(AgriCoopMember::class, 'member_id');
    }

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }
}
