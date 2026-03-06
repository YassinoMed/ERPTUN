<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpPriceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'unit',
        'unit_price',
        'description',
        'created_by',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];
}
