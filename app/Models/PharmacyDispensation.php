<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyDispensation extends Model
{
    protected $fillable = [
        'patient_id',
        'consultation_id',
        'prescription_id',
        'dispensed_by',
        'dispensed_at',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(PatientConsultation::class, 'consultation_id');
    }

    public function prescription()
    {
        return $this->belongsTo(PatientPrescription::class, 'prescription_id');
    }

    public function dispenser()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function items()
    {
        return $this->hasMany(PharmacyDispensationItem::class)->orderBy('id');
    }
}
