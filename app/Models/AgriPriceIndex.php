<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriPriceIndex extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_type',
        'market',
        'price',
        'currency',
        'recorded_at',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];
}
