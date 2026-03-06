<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRatePlan extends Model
{
    protected $fillable = [
        'room_type_id',
        'name',
        'base_rate',
        'min_rate',
        'max_rate',
        'currency',
        'created_by',
    ];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function recommendations()
    {
        return $this->hasMany(HotelPriceRecommendation::class, 'rate_plan_id');
    }
}
