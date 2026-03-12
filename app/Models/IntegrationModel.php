<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class IntegrationModel extends Model
{
    protected $connection = 'integrations';
}
