<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriParcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'area',
        'area_unit',
        'soil_type',
        'location',
        'created_by',
    ];

    protected $casts = [
        'area' => 'decimal:2',
    ];

    public function cropPlans()
    {
        return $this->hasMany(AgriCropPlan::class, 'parcel_id');
    }

    public function weatherAlerts()
    {
        return $this->hasMany(AgriWeatherAlert::class, 'parcel_id');
    }
}
