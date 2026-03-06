<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelMaintenanceIssue extends Model
{
    protected $fillable = [
        'room_id',
        'title',
        'description',
        'status',
        'priority',
        'reported_by',
        'assigned_to',
        'resolved_at',
        'created_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }
}
