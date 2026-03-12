<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriWeighing extends Model
{
    protected $fillable = [
        'lot_id',
        'cooperative_id',
        'producer_name',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'moisture_percent',
        'quality_grade',
        'weighing_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'gross_weight' => 'decimal:3',
        'tare_weight' => 'decimal:3',
        'net_weight' => 'decimal:3',
        'moisture_percent' => 'decimal:2',
        'weighing_date' => 'date',
    ];

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }

    public function cooperative()
    {
        return $this->belongsTo(AgriCooperative::class, 'cooperative_id');
    }
}
