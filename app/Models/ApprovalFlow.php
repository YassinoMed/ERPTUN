<?php

namespace App\Models;


class ApprovalFlow extends ApprovalModel
{
    protected $fillable = [
        'name',
        'resource_type',
        'min_amount',
        'max_amount',
        'default_sla_hours',
        'escalation_user_id',
        'allow_delegation',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'allow_delegation' => 'boolean',
    ];

    public function steps()
    {
        return $this->hasMany(ApprovalStep::class, 'approval_flow_id');
    }
}
