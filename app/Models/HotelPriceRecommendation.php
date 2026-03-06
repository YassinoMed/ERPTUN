<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPriceRecommendation extends Model
{
    protected $fillable = [
        'room_type_id',
        'rate_plan_id',
        'date',
        'recommended_rate',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function ratePlan()
    {
        return $this->belongsTo(HotelRatePlan::class, 'rate_plan_id');
    }
}
