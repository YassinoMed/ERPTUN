<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    protected $fillable = [
        'approval_flow_id',
        'name',
        'sequence',
        'rule',
        'created_by',
    ];
}
