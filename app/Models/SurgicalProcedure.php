<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurgicalProcedure extends Model
{
    protected $fillable = [
        'patient_id',
        'hospital_admission_id',
        'procedure_name',
        'surgeon_name',
        'theatre_name',
        'scheduled_at',
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
