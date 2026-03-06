<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpPriceQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'reference',
        'vat_rate',
        'subtotal',
        'vat_amount',
        'total',
        'created_by',
    ];

    protected $casts = [
        'vat_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(BtpPriceQuoteItem::class, 'quote_id');
    }
}
