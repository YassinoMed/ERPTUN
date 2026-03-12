<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class QualityModel extends Model
{
    protected $connection = 'quality';
}
