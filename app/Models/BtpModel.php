<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BtpModel extends Model
{
    protected $connection = 'btp';
}
