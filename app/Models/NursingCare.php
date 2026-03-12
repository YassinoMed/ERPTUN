<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingCare extends Model
{
    protected $fillable = [
        'patient_id',
        'hospital_admission_id',
        'care_type',
        'scheduled_at',
        'nurse_name',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function admission()
    {
        return $this->belongsTo(HospitalAdmission::class, 'hospital_admission_id');
    }
}
