<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAddon extends Model
{
    protected $fillable = [
        'plan_id',
        'name',
        'code',
        'description',
        'price',
        'billing_cycle',
        'limits',
        'is_active',
    ];

    protected $casts = [
        'limits' => 'array',
        'is_active' => 'boolean',
    ];
}
