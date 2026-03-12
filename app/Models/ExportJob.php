<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $fillable = [
        'created_by',
        'user_id',
        'module',
        'format',
        'filters',
        'status',
        'file_path',
        'scheduled_for',
        'started_at',
        'attempts',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'scheduled_for' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
