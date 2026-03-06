<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelChannelRate extends Model
{
    protected $fillable = [
        'channel_id',
        'room_type_id',
        'rate_plan_id',
        'date',
        'rate',
        'min_stay',
        'max_stay',
        'closed_to_arrival',
        'closed_to_departure',
    ];

    protected $casts = [
        'date' => 'date',
        'closed_to_arrival' => 'boolean',
        'closed_to_departure' => 'boolean',
    ];

    public function channel()
    {
        return $this->belongsTo(HotelChannel::class, 'channel_id');
    }

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function ratePlan()
    {
        return $this->belongsTo(HotelRatePlan::class, 'rate_plan_id');
    }
}
