<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpSitePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'file',
        'latitude',
        'longitude',
        'taken_at',
        'note',
        'created_by',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
}
