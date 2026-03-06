<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriCoopMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooperative_id',
        'name',
        'member_code',
        'share_percent',
        'advance_balance',
        'contact_phone',
        'created_by',
    ];

    protected $casts = [
        'share_percent' => 'decimal:2',
        'advance_balance' => 'decimal:2',
    ];

    public function cooperative()
    {
        return $this->belongsTo(AgriCooperative::class, 'cooperative_id');
    }

    public function deliveries()
    {
        return $this->hasMany(AgriHarvestDelivery::class, 'member_id');
    }

    public function payouts()
    {
        return $this->hasMany(AgriMemberPayout::class, 'member_id');
    }
}
