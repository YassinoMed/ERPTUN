<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPortalMessage extends Model
{
    protected $fillable = [
        'patient_id',
        'direction',
        'subject',
        'message',
        'sent_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
