<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercialContract extends Model
{
    protected $fillable = [
        'contract_number',
        'title',
        'party_type',
        'party_id',
        'retail_store_id',
        'amount',
        'category',
        'owner_name',
        'billing_cycle',
        'renewal_notice_days',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function retailStore()
    {
        return $this->belongsTo(RetailStore::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'party_id');
    }

    public function vender()
    {
        return $this->belongsTo(Vender::class, 'party_id');
    }

    public function getPartyNameAttribute()
    {
        if ($this->party_type === 'customer') {
            return optional($this->customer)->name;
        }

        if ($this->party_type === 'vender') {
            return optional($this->vender)->name;
        }

        return null;
    }
}
