<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportShipment extends Model
{
    protected $fillable = [
        'reference',
        'customer_id',
        'origin',
        'destination',
        'vehicle_number',
        'driver_name',
        'departure_date',
        'arrival_date',
        'status',
        'freight_amount',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'planned' => 'Planned',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'returned' => 'Returned',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
