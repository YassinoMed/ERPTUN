<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensitiveAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'created_by',
        'resource_type',
        'resource_id',
        'action',
        'route',
        'ip_address',
        'user_agent',
        'context',
        'created_at',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];
}
