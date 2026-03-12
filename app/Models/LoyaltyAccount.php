<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyAccount extends Model
{
    protected $fillable = [
        'customer_id',
        'code',
        'points_balance',
        'tier',
        'status',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
