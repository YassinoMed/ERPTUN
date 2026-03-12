<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class CooperativeModel extends Model
{
    protected $connection = 'cooperative';
}
