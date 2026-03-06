<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelInventoryMovement extends Model
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
