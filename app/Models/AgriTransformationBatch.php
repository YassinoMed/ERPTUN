<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgriTransformationBatch extends TraceabilityModel
{
    use HasFactory;

    protected $fillable = [
        'input_lot_id',
        'output_lot_id',
        'batch_number',
        'process_step',
        'facility_name',
        'input_quantity',
        'output_quantity',
        'waste_quantity',
        'processed_at',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'input_quantity' => 'decimal:3',
        'output_quantity' => 'decimal:3',
        'waste_quantity' => 'decimal:3',
        'processed_at' => 'datetime',
    ];

    public function inputLot()
    {
        return $this->belongsTo(AgriLot::class, 'input_lot_id');
    }

    public function outputLot()
    {
        return $this->belongsTo(AgriLot::class, 'output_lot_id');
    }
}
