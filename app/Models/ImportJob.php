<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    protected $fillable = [
        'created_by',
        'user_id',
        'module',
        'file_name',
        'mapping',
        'preview_data',
        'status',
        'summary',
        'rollback_payload',
        'processed_at',
    ];

    protected $casts = [
        'mapping' => 'array',
        'preview_data' => 'array',
        'summary' => 'array',
        'rollback_payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
