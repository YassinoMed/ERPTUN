<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class TraceabilityModel extends Model
{
    protected $connection = 'traceability';
}
