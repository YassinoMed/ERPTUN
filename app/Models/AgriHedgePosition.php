<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriHedgePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'instrument',
        'position_type',
        'quantity',
        'price',
        'opened_at',
        'closed_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'price' => 'decimal:2',
        'opened_at' => 'date',
        'closed_at' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(AgriPurchaseContract::class, 'contract_id');
    }
}
