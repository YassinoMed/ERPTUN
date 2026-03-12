<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $fillable = [
        'patient_id',
        'consultation_id',
        'panel_name',
        'sample_type',
        'status',
        'critical_flag',
        'ordered_at',
        'collected_at',
        'validated_at',
        'result_summary',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'critical_flag' => 'boolean',
        'ordered_at' => 'datetime',
        'collected_at' => 'datetime',
        'validated_at' => 'datetime',
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
