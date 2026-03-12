<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientConsent extends Model
{
    protected $fillable = [
        'patient_id',
        'title',
        'status',
        'consented_at',
        'expires_at',
        'notes',
        'file_path',
        'created_by',
    ];

    protected $casts = [
        'consented_at' => 'date',
        'expires_at' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
