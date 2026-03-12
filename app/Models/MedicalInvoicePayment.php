<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalInvoicePayment extends Model
{
    protected $fillable = [
        'medical_invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(MedicalInvoice::class, 'medical_invoice_id');
    }
}
