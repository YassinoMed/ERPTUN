<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalInvoice extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'consultation_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'insurer_name',
        'total_amount',
        'insurance_amount',
        'patient_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'patient_amount' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(PatientConsultation::class, 'consultation_id');
    }

    public function appointment()
    {
        return $this->belongsTo(MedicalAppointment::class, 'appointment_id');
    }

    public function items()
    {
        return $this->hasMany(MedicalInvoiceItem::class)->orderBy('id');
    }

    public function payments()
    {
        return $this->hasMany(MedicalInvoicePayment::class)->orderByDesc('payment_date');
    }

    public function paidAmount()
    {
        return (float) $this->payments()->sum('amount');
    }

    public function dueAmount()
    {
        return max(0, (float) $this->patient_amount - $this->paidAmount());
    }
}
