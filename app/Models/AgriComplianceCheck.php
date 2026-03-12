<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgriComplianceCheck extends TraceabilityModel
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'control_type',
        'result',
        'certificate_ref',
        'measured_value',
        'threshold_value',
        'checked_at',
        'corrective_action',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }
}
