<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantOnboardingChecklist extends Model
{
    protected $fillable = [
        'created_by',
        'checklist',
        'completed_steps',
        'configured_by',
        'completed_at',
    ];

    protected $casts = [
        'checklist' => 'array',
        'completed_steps' => 'array',
        'completed_at' => 'datetime',
    ];
}
