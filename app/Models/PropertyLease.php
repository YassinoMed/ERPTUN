<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyLease extends Model
{
    protected $fillable = [
        'managed_property_id',
        'property_unit_id',
        'customer_id',
        'reference',
        'billing_cycle',
        'status',
        'start_date',
        'end_date',
        'renewal_date',
        'rent_amount',
        'deposit_amount',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'pending_renewal' => 'Pending Renewal',
        'terminated' => 'Terminated',
        'expired' => 'Expired',
    ];

    public static $billingCycles = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly',
    ];

    public function property()
    {
        return $this->hasOne(ManagedProperty::class, 'id', 'managed_property_id');
    }

    public function unit()
    {
        return $this->hasOne(PropertyUnit::class, 'id', 'property_unit_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
