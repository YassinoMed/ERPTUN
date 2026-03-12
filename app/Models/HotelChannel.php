<?php

namespace App\Models;


class HotelChannel extends HotelModel
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'credentials',
        'sync_status',
        'last_synced_at',
        'created_by',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function reservations()
    {
        return $this->hasMany(HotelReservation::class, 'channel_id');
    }

    public function syncLogs()
    {
        return $this->hasMany(HotelChannelSyncLog::class, 'channel_id');
    }
}
