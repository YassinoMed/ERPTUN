<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpSubcontractInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcontractor_id',
        'project_id',
        'reference',
        'amount',
        'retention_rate',
        'retention_amount',
        'vat_rate',
        'vat_amount',
        'total_due',
        'status',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'retention_rate' => 'decimal:2',
        'retention_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_due' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function subcontractor()
    {
        return $this->hasOne(BtpSubcontractor::class, 'id', 'subcontractor_id');
    }

    public function payments()
    {
        return $this->hasMany(BtpSubcontractPayment::class, 'invoice_id');
    }
}
