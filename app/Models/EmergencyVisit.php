<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyVisit extends Model
{
    protected $fillable = [
        'patient_id',
        'triage_level',
        'chief_complaint',
        'arrived_at',
        'attending_doctor',
        'status',
        'disposition',
        'created_by',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
