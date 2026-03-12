<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleAlert extends Model
{
    protected $fillable = [
        'owner_id',
        'module_key',
        'alert_key',
        'severity',
        'status',
        'title',
        'message',
        'payload',
        'detected_at',
        'resolved_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
}
