<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeasingContract extends Model
{
    protected $fillable = [
        'customer_id',
        'contract_number',
        'asset_name',
        'lease_amount',
        'residual_amount',
        'start_date',
        'end_date',
        'payment_frequency',
        'status',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'completed' => 'Completed',
        'terminated' => 'Terminated',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
