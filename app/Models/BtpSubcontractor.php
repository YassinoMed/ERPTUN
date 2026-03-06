<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpSubcontractor extends Model
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
