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
        'reason_for_visit',
        'temperature',
        'heart_rate',
        'blood_pressure',
        'respiratory_rate',
        'weight',
        'height',
        'next_visit_date',
        'diagnosis',
        'clinical_observations',
        'requested_exams',
        'medical_certificate',
        'sick_leave_start',
        'sick_leave_end',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'next_visit_date' => 'date',
        'temperature' => 'decimal:2',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'sick_leave_start' => 'date',
        'sick_leave_end' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(PatientPrescription::class, 'consultation_id');
    }

    public function labResults()
    {
        return $this->hasMany(PatientLabResult::class, 'consultation_id');
    }
}
