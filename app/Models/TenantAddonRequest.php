<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantAddonRequest extends Model
{
    protected $fillable = [
        'created_by',
        'plan_addon_id',
        'requested_by',
        'reviewed_by',
        'status',
        'billing_cycle',
        'amount',
        'request_note',
        'review_note',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function addon()
    {
        return $this->belongsTo(PlanAddon::class, 'plan_addon_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
