<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRecommendation extends Model
{
    protected $fillable = [
        'owner_id',
        'module_key',
        'recommendation_key',
        'priority',
        'status',
        'title',
        'description',
        'payload',
        'generated_at',
        'applied_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'generated_at' => 'datetime',
        'applied_at' => 'datetime',
    ];
}
