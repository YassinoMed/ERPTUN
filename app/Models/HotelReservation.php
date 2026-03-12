<?php

namespace App\Models;


class HotelReservation extends HotelModel
{
    protected $fillable = [
        'channel_id',
        'room_type_id',
        'external_reservation_id',
        'guest_name',
        'guest_email',
        'check_in',
        'check_out',
        'status',
        'total_amount',
        'currency',
        'created_by',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    public function channel()
    {
        return $this->belongsTo(HotelChannel::class, 'channel_id');
    }

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }
}
