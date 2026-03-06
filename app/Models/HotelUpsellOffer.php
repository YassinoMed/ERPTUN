<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelUpsellOffer extends Model
{
    protected $fillable = [
        'reservation_id',
        'customer_id',
        'room_type_id',
        'stay_length_min',
        'stay_length_max',
        'segments',
        'offer_payload',
        'status',
        'created_by',
    ];

    protected $casts = [
        'segments' => 'array',
        'offer_payload' => 'array',
    ];

    public function reservation()
    {
        return $this->belongsTo(HotelReservation::class, 'reservation_id');
    }

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function conversions()
    {
        return $this->hasMany(HotelUpsellConversion::class, 'offer_id');
    }
}
