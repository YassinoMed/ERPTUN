<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleKpiSnapshot extends Model
{
    protected $fillable = [
        'owner_id',
        'module_key',
        'kpis',
        'calculated_at',
    ];

    protected $casts = [
        'kpis' => 'array',
        'calculated_at' => 'datetime',
    ];
}
