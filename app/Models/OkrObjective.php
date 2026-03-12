<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OkrObjective extends Model
{
    protected $fillable = [
        'title',
        'owner_id',
        'project_id',
        'cycle',
        'status',
        'progress',
        'start_date',
        'end_date',
        'description',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'at_risk' => 'At Risk',
        'completed' => 'Completed',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function keyResults()
    {
        return $this->hasMany(OkrKeyResult::class)->latest('id');
    }
}
