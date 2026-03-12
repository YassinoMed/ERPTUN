<?php

namespace App\Models;


class HotelInventoryItem extends HotelModel
{
    protected $fillable = [
        'name',
        'sku',
        'unit',
        'quantity_on_hand',
        'reorder_level',
        'created_by',
    ];

    public function movements()
    {
        return $this->hasMany(HotelInventoryMovement::class, 'inventory_item_id');
    }
}
