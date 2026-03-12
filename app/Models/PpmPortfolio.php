<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpmPortfolio extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'status',
        'priority',
        'start_date',
        'end_date',
        'description',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'closed' => 'Closed',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function initiatives()
    {
        return $this->hasMany(PpmInitiative::class)->latest('id');
    }
}
