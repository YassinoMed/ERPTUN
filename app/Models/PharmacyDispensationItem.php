<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyDispensationItem extends Model
{
    protected $fillable = [
        'pharmacy_dispensation_id',
        'pharmacy_medication_id',
        'quantity',
        'dosage',
        'frequency',
        'duration',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function dispensation()
    {
        return $this->belongsTo(PharmacyDispensation::class, 'pharmacy_dispensation_id');
    }

    public function medication()
    {
        return $this->belongsTo(PharmacyMedication::class, 'pharmacy_medication_id');
    }
}
