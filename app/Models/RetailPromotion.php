<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailPromotion extends Model
{
    protected $fillable = [
        'name',
        'code',
        'promotion_type',
        'scope_type',
        'retail_store_id',
        'audience_type',
        'auto_apply',
        'discount_value',
        'minimum_amount',
        'budget_amount',
        'starts_at',
        'ends_at',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'budget_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'auto_apply' => 'boolean',
    ];

    public function retailStore()
    {
        return $this->belongsTo(RetailStore::class);
    }
}
