<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantUsage extends Model
{
    protected $fillable = [
        'created_by',
        'metric_key',
        'subject_type',
        'subject_id',
        'usage_value',
        'limit_value',
        'resets_at',
    ];

    protected $casts = [
        'usage_value' => 'decimal:2',
        'limit_value' => 'decimal:2',
        'resets_at' => 'datetime',
    ];
}
