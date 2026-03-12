<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InnovationIdea extends Model
{
    protected $fillable = [
        'title',
        'category',
        'submitted_by',
        'status',
        'priority',
        'expected_value',
        'description',
        'business_case',
        'implementation_notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'implemented' => 'Implemented',
    ];

    public static $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    public function submitter()
    {
        return $this->hasOne(Employee::class, 'id', 'submitted_by');
    }
}
