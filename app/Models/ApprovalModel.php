<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ApprovalModel extends Model
{
    protected $connection = 'approvals';
}
