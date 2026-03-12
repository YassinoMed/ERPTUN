<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantPlanAddon extends Model
{
    protected $fillable = [
        'created_by',
        'plan_addon_id',
        'status',
        'amount',
        'billing_cycle',
        'activated_at',
        'expires_at',
        'renews_at',
        'cancelled_at',
        'cancel_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'renews_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function addon()
    {
        return $this->belongsTo(PlanAddon::class, 'plan_addon_id');
    }
}
