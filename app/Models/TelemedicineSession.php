<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelemedicineSession extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'provider_name',
        'session_link',
        'scheduled_at',
        'status',
        'summary',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(MedicalAppointment::class, 'appointment_id');
    }
}
