<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgriWeatherAlert extends CropPlanningModel
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'alert_type',
        'severity',
        'message',
        'alert_date',
        'acknowledged_at',
        'created_by',
    ];

    protected $casts = [
        'alert_date' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function parcel()
    {
        return $this->belongsTo(AgriParcel::class, 'parcel_id');
    }
}
