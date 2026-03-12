<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriColdStorageRecord extends Model
{
    protected $fillable = [
        'lot_id',
        'facility_name',
        'chamber_name',
        'temperature',
        'humidity',
        'quantity',
        'entry_date',
        'expiry_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'quantity' => 'decimal:3',
        'entry_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }
}
