<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedModuleFeatureState extends Model
{
    protected $fillable = [
        'owner_id',
        'module_key',
        'feature_key',
        'status',
        'priority',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
