<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPrescription extends Model
{
    protected $fillable = [
        'consultation_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration',
        'notes',
        'created_by',
    ];

    public function consultation()
    {
        return $this->belongsTo(PatientConsultation::class, 'consultation_id');
    }
}
