<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'approval_flow_id',
        'resource_type',
        'resource_id',
        'status',
        'requested_by',
        'created_by',
    ];
}
