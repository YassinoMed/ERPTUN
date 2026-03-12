<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailProcurementRequest extends Model
{
    protected $fillable = [
        'retail_store_id',
        'vender_id',
        'category_id',
        'reference',
        'title',
        'budget_amount',
        'needed_by',
        'status',
        'notes',
        'approved_by',
        'created_by',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'needed_by' => 'date',
    ];

    public function retailStore()
    {
        return $this->belongsTo(RetailStore::class);
    }

    public function vender()
    {
        return $this->belongsTo(Vender::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductServiceCategory::class, 'category_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
