<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelChannelSyncLog extends Model
{
    protected $fillable = [
        'channel_id',
        'status',
        'direction',
        'message',
        'payload',
        'synced_at',
        'created_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(HotelChannel::class, 'channel_id');
    }
}
