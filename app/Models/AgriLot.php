<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgriLot extends TraceabilityModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'crop_type',
        'source_reference',
        'parcel_origin',
        'harvest_date',
        'expiry_date',
        'quantity',
        'unit',
        'status',
        'quality_status',
        'created_by',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'decimal:3',
    ];

    public function traceEvents()
    {
        return $this->hasMany(AgriTraceEvent::class, 'lot_id');
    }

    public function certificate()
    {
        return $this->hasOne(AgriCertificate::class, 'lot_id');
    }

    public function inboundTransformations()
    {
        return $this->hasMany(AgriTransformationBatch::class, 'output_lot_id')->latest('processed_at');
    }

    public function outboundTransformations()
    {
        return $this->hasMany(AgriTransformationBatch::class, 'input_lot_id')->latest('processed_at');
    }

    public function complianceChecks()
    {
        return $this->hasMany(AgriComplianceCheck::class, 'lot_id')->latest('checked_at');
    }
}
