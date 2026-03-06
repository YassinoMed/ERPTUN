<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalFlow extends Model
{
    protected $fillable = [
        'name',
        'resource_type',
        'is_active',
        'created_by',
    ];
}
