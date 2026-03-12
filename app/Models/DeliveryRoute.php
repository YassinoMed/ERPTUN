<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'name',
        'driver_name',
        'vehicle_no',
        'route_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'route_date' => 'date',
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
}
