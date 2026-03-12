<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalInvoiceItem extends Model
{
    protected $fillable = [
        'medical_invoice_id',
        'medical_service_id',
        'description',
        'quantity',
        'unit_price',
        'coverage_rate',
        'covered_amount',
        'patient_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'coverage_rate' => 'decimal:2',
        'covered_amount' => 'decimal:2',
        'patient_amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(MedicalInvoice::class, 'medical_invoice_id');
    }

    public function service()
    {
        return $this->belongsTo(MedicalService::class, 'medical_service_id');
    }
}
