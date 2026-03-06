<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriMemberPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'member_id',
        'gross_amount',
        'advance_deducted',
        'net_amount',
        'created_by',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'advance_deducted' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function distribution()
    {
        return $this->belongsTo(AgriRevenueDistribution::class, 'distribution_id');
    }

    public function member()
    {
        return $this->belongsTo(AgriCoopMember::class, 'member_id');
    }
}
