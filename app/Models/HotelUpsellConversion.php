<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelUpsellConversion extends Model
{
    protected $fillable = [
        'offer_id',
        'service_id',
        'quantity',
        'total_amount',
        'converted_at',
        'created_by',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    public function offer()
    {
        return $this->belongsTo(HotelUpsellOffer::class, 'offer_id');
    }

    public function service()
    {
        return $this->belongsTo(HotelUpsellService::class, 'service_id');
    }
}
