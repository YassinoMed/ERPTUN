<?php

namespace App\Models;


class HotelDemandForecast extends HotelModel
{
    protected $fillable = [
        'room_type_id',
        'date',
        'demand_score',
        'occupancy_rate',
        'seasonal_factor',
        'event_factor',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }
}
