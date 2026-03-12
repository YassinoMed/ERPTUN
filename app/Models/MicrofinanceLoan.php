<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MicrofinanceLoan extends Model
{
    protected $fillable = [
        'customer_id',
        'loan_number',
        'principal_amount',
        'interest_rate',
        'installment_amount',
        'start_date',
        'maturity_date',
        'status',
        'purpose',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'approved' => 'Approved',
        'active' => 'Active',
        'closed' => 'Closed',
        'defaulted' => 'Defaulted',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
