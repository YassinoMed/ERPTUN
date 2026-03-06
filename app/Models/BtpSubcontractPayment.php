<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpSubcontractPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->hasOne(BtpSubcontractInvoice::class, 'id', 'invoice_id');
    }
}
