<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSession extends Model
{
    protected $fillable = [
        'cash_register_id',
        'retail_store_id',
        'opened_by',
        'opened_at',
        'closed_at',
        'expected_amount',
        'actual_amount',
        'variance_amount',
        'transactions_count',
        'mixed_payment_enabled',
        'session_mode',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance_amount' => 'decimal:2',
        'mixed_payment_enabled' => 'boolean',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function retailStore()
    {
        return $this->belongsTo(RetailStore::class);
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }
}
