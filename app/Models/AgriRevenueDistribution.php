<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriRevenueDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooperative_id',
        'period_start',
        'period_end',
        'total_revenue',
        'distribution_method',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_revenue' => 'decimal:2',
    ];

    public function cooperative()
    {
        return $this->belongsTo(AgriCooperative::class, 'cooperative_id');
    }

    public function payouts()
    {
        return $this->hasMany(AgriMemberPayout::class, 'distribution_id');
    }
}
