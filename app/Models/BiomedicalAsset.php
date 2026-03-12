<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiomedicalAsset extends Model
{
    protected $fillable = [
        'name',
        'asset_code',
        'equipment_type',
        'serial_number',
        'location',
        'calibration_due_date',
        'maintenance_status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'calibration_due_date' => 'date',
    ];

    public function isDueForCalibration()
    {
        return $this->calibration_due_date !== null && $this->calibration_due_date->isPast();
    }
}
