<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelInventoryItem extends Model
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
