<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanRequest extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'current_plan_id',
        'duration',
        'status',
        'request_note',
        'review_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }

    public function currentPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'current_plan_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function reviewer()
    {
        return $this->hasOne('App\Models\User', 'id', 'reviewed_by');
    }
}
