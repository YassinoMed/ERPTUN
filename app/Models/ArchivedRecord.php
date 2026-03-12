<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedRecord extends Model
{
    protected $fillable = [
        'created_by',
        'record_owner_id',
        'record_type',
        'record_id',
        'display_name',
        'reason',
        'archived_by',
        'archived_at',
        'restored_by',
        'restored_at',
        'payload',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'restored_at' => 'datetime',
        'payload' => 'array',
    ];
}
