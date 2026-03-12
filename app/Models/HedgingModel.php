<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class HedgingModel extends Model
{
    protected $connection = 'hedging';
}
