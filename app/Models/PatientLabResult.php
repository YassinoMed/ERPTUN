<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientLabResult extends Model
{
    protected $fillable = [
        'patient_id',
        'consultation_id',
        'test_name',
        'result_value',
        'unit',
        'reference_range',
        'result_date',
        'notes',
        'created_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(PatientConsultation::class, 'consultation_id');
    }
}
