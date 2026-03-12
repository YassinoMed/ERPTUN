<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model
{
    protected $fillable = [
        'created_by',
        'user_id',
        'name',
        'report_type',
        'filters',
        'columns',
        'is_shared',
        'last_run_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'is_shared' => 'boolean',
        'last_run_at' => 'datetime',
    ];
}
