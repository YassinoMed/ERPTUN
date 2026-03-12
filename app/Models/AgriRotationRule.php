<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgriRotationRule extends CropPlanningModel
{
    use HasFactory;

    protected $fillable = [
        'crop_name',
        'follow_crop_name',
        'min_gap_days',
        'recommendation',
        'created_by',
    ];
}
