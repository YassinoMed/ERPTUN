<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class MrpModel extends Model
{
    protected $connection = 'mrp';
}
