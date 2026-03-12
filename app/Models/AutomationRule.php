<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'event_name',
        'description',
        'conditions',
        'actions',
        'priority',
        'is_active',
        'last_triggered_at',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];
}
