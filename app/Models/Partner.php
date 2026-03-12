<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'partner_code',
        'name',
        'partner_type',
        'status',
        'contact_name',
        'email',
        'phone',
        'website',
        'customer_id',
        'vender_id',
        'notes',
        'created_by',
    ];

    public static $types = [
        'reseller' => 'Reseller',
        'implementation' => 'Implementation',
        'referral' => 'Referral',
        'technology' => 'Technology',
    ];

    public static $statuses = [
        'active' => 'Active',
        'onboarding' => 'Onboarding',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vender()
    {
        return $this->belongsTo(Vender::class);
    }
}
