<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpmInitiative extends Model
{
    protected $fillable = [
        'ppm_portfolio_id',
        'project_id',
        'sponsor_id',
        'name',
        'status',
        'health_status',
        'budget',
        'target_value',
        'achieved_value',
        'start_date',
        'end_date',
        'description',
        'created_by',
    ];

    public static $statuses = [
        'planned' => 'Planned',
        'active' => 'Active',
        'at_risk' => 'At Risk',
        'completed' => 'Completed',
    ];

    public static $healthStatuses = [
        'green' => 'Green',
        'amber' => 'Amber',
        'red' => 'Red',
    ];

    public function portfolio()
    {
        return $this->belongsTo(PpmPortfolio::class, 'ppm_portfolio_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }
}
