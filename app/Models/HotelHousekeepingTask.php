<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelHousekeepingTask extends Model
{
    protected $fillable = [
        'room_id',
        'status',
        'priority',
        'assigned_to',
        'scheduled_at',
        'started_at',
        'completed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function items()
    {
        return $this->hasMany(HotelHousekeepingTaskItem::class, 'task_id');
    }
}
