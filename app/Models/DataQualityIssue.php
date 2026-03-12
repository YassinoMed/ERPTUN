<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataQualityIssue extends Model
{
    protected $fillable = [
        'created_by',
        'issue_type',
        'module',
        'record_type',
        'record_id',
        'duplicate_type',
        'duplicate_id',
        'status',
        'payload',
        'resolved_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'resolved_at' => 'datetime',
    ];
}
