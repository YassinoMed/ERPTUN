<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPricingRule extends Model
{
    protected $fillable = [
        'name',
        'room_type_id',
        'customer_segment',
        'min_rate',
        'max_rate',
        'margin',
        'occupancy_threshold',
        'seasonality_multiplier',
        'event_multiplier',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }
}
