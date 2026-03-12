<?php

namespace App\Models;


class HotelRoomType extends HotelModel
{
    protected $fillable = [
        'name',
        'code',
        'base_capacity',
        'created_by',
    ];

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class, 'room_type_id');
    }

    public function ratePlans()
    {
        return $this->hasMany(HotelRatePlan::class, 'room_type_id');
    }
}
