<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalAppointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'room',
        'specialty',
        'start_at',
        'end_at',
        'status',
        'reminder_channel',
        'reminder_at',
        'reminder_sent_at',
        'canceled_at',
        'cancel_reason',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reminder_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
