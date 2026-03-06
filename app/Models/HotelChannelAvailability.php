<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelChannelAvailability extends Model
{
    protected $fillable = [
        'channel_id',
        'room_type_id',
        'date',
        'available',
        'stop_sell',
    ];

    protected $casts = [
        'date' => 'date',
        'stop_sell' => 'boolean',
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
