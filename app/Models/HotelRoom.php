<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    protected $fillable = [
        'room_type_id',
        'name',
        'floor',
        'status',
        'created_by',
    ];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HotelHousekeepingTask::class, 'room_id');
    }

    public function maintenanceIssues()
    {
        return $this->hasMany(HotelMaintenanceIssue::class, 'room_id');
    }
}
