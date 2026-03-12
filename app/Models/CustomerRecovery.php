<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRecovery extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_id',
        'reference',
        'stage',
        'priority',
        'due_amount',
        'next_follow_up_date',
        'last_contact_date',
        'assigned_to',
        'status',
        'notes',
        'created_by',
    ];

    public static $stages = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'negotiation' => 'Negotiation',
        'promise_to_pay' => 'Promise To Pay',
        'legal' => 'Legal',
    ];

    public static $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public static $statuses = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function assignee()
    {
        return $this->hasOne(Employee::class, 'id', 'assigned_to');
    }
}
