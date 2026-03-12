<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ProductionModel extends Model
{
    protected $connection = 'production';
}
