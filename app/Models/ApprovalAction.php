<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalAction extends Model
{
    protected $fillable = [
        'approval_request_id',
        'approval_step_id',
        'action',
        'comment',
        'acted_by',
        'created_by',
    ];
}
