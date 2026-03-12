<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'policy_number',
        'provider_name',
        'policy_name',
        'coverage_type',
        'insured_party',
        'insured_asset',
        'start_date',
        'end_date',
        'premium_amount',
        'coverage_amount',
        'status',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
        'closed' => 'Closed',
    ];

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }
}
