<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoomType extends Model
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
