<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataConsent extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_name',
        'subject_reference',
        'purpose',
        'channel',
        'status',
        'consented_at',
        'expires_at',
        'evidence_reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'consented_at' => 'date',
        'expires_at' => 'date',
    ];

    public static $statuses = [
        'granted' => 'Granted',
        'revoked' => 'Revoked',
        'expired' => 'Expired',
        'pending' => 'Pending',
    ];
}
