<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelHousekeepingChecklist extends Model
{
    protected $fillable = [
        'name',
        'room_type_id',
        'created_by',
    ];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function items()
    {
        return $this->hasMany(HotelHousekeepingChecklistItem::class, 'checklist_id');
    }
}
