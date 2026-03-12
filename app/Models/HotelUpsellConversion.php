<?php

namespace App\Models;


class HotelUpsellConversion extends HotelModel
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
