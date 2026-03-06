<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpPriceQuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'price_item_id',
        'description',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function priceItem()
    {
        return $this->hasOne(BtpPriceItem::class, 'id', 'price_item_id');
    }
}
