<?php

namespace App\Models;

class WorkflowDelegation extends ApprovalModel
{
    protected $fillable = [
        'user_id',
        'delegate_user_id',
        'starts_at',
        'ends_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isEffective(?\Carbon\CarbonInterface $at = null): bool
    {
        $at = $at ?: now();

        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->gt($at)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lt($at)) {
            return false;
        }

        return true;
    }
}
