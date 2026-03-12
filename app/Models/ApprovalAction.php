<?php

namespace App\Models;


class ApprovalAction extends ApprovalModel
{
    protected $fillable = [
        'approval_request_id',
        'approval_step_id',
        'action',
        'comment',
        'metadata',
        'acted_by',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }

    public function step()
    {
        return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    }
}
