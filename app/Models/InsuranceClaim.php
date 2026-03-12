<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    protected $fillable = [
        'insurance_policy_id',
        'customer_id',
        'assigned_to',
        'claim_number',
        'incident_date',
        'reported_date',
        'amount_claimed',
        'amount_settled',
        'priority',
        'status',
        'incident_type',
        'location',
        'description',
        'resolution_notes',
        'created_by',
    ];

    public static $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'settled' => 'Settled',
        'closed' => 'Closed',
    ];

    public function policy()
    {
        return $this->hasOne(InsurancePolicy::class, 'id', 'insurance_policy_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function assignee()
    {
        return $this->hasOne(Employee::class, 'id', 'assigned_to');
    }
}
