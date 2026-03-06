<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriCooperative extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'region',
        'currency',
        'created_by',
    ];

    public function members()
    {
        return $this->hasMany(AgriCoopMember::class, 'cooperative_id');
    }

    public function deliveries()
    {
        return $this->hasMany(AgriHarvestDelivery::class, 'cooperative_id');
    }

    public function distributions()
    {
        return $this->hasMany(AgriRevenueDistribution::class, 'cooperative_id');
    }
}
