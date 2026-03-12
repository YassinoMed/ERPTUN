<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreReplenishmentRequest extends Model
{
    protected $fillable = [
        'source_store_id',
        'destination_store_id',
        'product_id',
        'suggested_quantity',
        'approved_quantity',
        'needed_by',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'suggested_quantity' => 'decimal:3',
        'approved_quantity' => 'decimal:3',
        'needed_by' => 'date',
    ];

    public function sourceStore()
    {
        return $this->belongsTo(RetailStore::class, 'source_store_id');
    }

    public function destinationStore()
    {
        return $this->belongsTo(RetailStore::class, 'destination_store_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductService::class, 'product_id');
    }
}
