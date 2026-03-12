<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriExportShipment extends Model
{
    protected $fillable = [
        'lot_id',
        'shipment_ref',
        'customer_name',
        'destination_country',
        'container_no',
        'incoterm',
        'shipped_quantity',
        'departure_date',
        'status',
        'document_ref',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'shipped_quantity' => 'decimal:3',
        'departure_date' => 'date',
    ];

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }
}
