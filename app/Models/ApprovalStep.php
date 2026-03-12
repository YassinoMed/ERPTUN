<?php

namespace App\Models;


class ApprovalStep extends ApprovalModel
{
    protected $fillable = [
        'approval_flow_id',
        'name',
        'sequence',
        'approver_type',
        'approver_id',
        'threshold_min',
        'threshold_max',
        'sla_hours',
        'escalation_user_id',
        'require_reject_reason',
        'rule',
        'created_by',
    ];

    protected $casts = [
        'rule' => 'array',
        'threshold_min' => 'decimal:2',
        'threshold_max' => 'decimal:2',
        'require_reject_reason' => 'boolean',
    ];

    public function flow()
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }
}
