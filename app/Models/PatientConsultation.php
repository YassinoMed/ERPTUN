<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientConsultation extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'consultation_date',
        'doctor_name',
        'title',
        'next_visit_date',
        'diagnosis',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'next_visit_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(PatientPrescription::class, 'consultation_id');
    }
}
