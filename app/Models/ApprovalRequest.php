<?php

namespace App\Models;


class ApprovalRequest extends ApprovalModel
{
    protected $fillable = [
        'approval_flow_id',
        'current_step_id',
        'resource_type',
        'resource_id',
        'status',
        'requested_by',
        'delegated_to',
        'due_at',
        'escalated_at',
        'context',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'escalated_at' => 'datetime',
        'context' => 'array',
    ];

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function currentStep()
    {
        return $this->belongsTo(ApprovalStep::class, 'current_step_id');
    }

    public function actions()
    {
        return $this->hasMany(ApprovalAction::class, 'approval_request_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function delegatedUser()
    {
        return $this->belongsTo(User::class, 'delegated_to');
    }
}
