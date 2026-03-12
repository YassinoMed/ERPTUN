<?php

namespace App\Models;


class HotelInventoryMovement extends HotelModel
{
    protected $fillable = [
        'inventory_item_id',
        'quantity',
        'type',
        'reason',
        'created_by',
    ];

    public function item()
    {
        return $this->belongsTo(HotelInventoryItem::class, 'inventory_item_id');
    }
}
