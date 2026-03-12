<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class CropPlanningModel extends Model
{
    protected $connection = 'crop_planning';
}
