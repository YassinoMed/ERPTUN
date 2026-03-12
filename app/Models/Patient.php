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
        'medical_history',
        'current_treatments',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'photo_path',
        'created_by',
        'archived_at',
        'archived_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'archived_at' => 'datetime',
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

    public function documents()
    {
        return $this->hasMany(PatientDocument::class)->orderByDesc('id');
    }

    public function consents()
    {
        return $this->hasMany(PatientConsent::class)->orderByDesc('consented_at');
    }

    public function accessLogs()
    {
        return $this->hasMany(MedicalRecordAccessLog::class)->orderByDesc('id');
    }
}
