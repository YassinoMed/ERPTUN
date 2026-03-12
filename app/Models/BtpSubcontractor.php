<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BtpSubcontractor extends BtpModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'created_by',
    ];

    public function invoices()
    {
        return $this->hasMany(BtpSubcontractInvoice::class, 'subcontractor_id');
    }
}
