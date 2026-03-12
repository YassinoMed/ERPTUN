<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalAdmission extends Model
{
    protected $fillable = [
        'patient_id',
        'attending_doctor_id',
        'room_id',
        'bed_id',
        'admission_date',
        'discharge_date',
        'status',
        'reason',
        'diagnosis',
        'care_plan',
        'discharge_summary',
        'created_by',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'attending_doctor_id');
    }

    public function room()
    {
        return $this->belongsTo(HospitalRoom::class, 'room_id');
    }

    public function bed()
    {
        return $this->belongsTo(HospitalBed::class, 'bed_id');
    }
}
