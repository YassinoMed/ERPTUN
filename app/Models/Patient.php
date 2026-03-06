<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'cin',
        'cnam_number',
        'gender',
        'blood_group',
        'birth_date',
        'phone',
        'email',
        'address',
        'allergies',
        'photo_path',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function consultations()
    {
        return $this->hasMany(PatientConsultation::class);
    }

    public function appointments()
    {
        return $this->hasMany(MedicalAppointment::class);
    }

    public function labResults()
    {
        return $this->hasMany(PatientLabResult::class);
    }
}
