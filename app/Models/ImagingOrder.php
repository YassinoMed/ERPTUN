<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagingOrder extends Model
{
    protected $fillable = [
        'patient_id',
        'consultation_id',
        'modality',
        'body_part',
        'requested_by',
        'scheduled_at',
        'status',
        'report_summary',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
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
