<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustrialResource extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'type',
        'parent_id',
        'branch_id',
        'manager_id',
        'code',
        'name',
        'status',
        'capacity_hours_per_day',
        'capacity_workers',
        'notes',
        'created_by',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function manager()
    {
        return $this->hasOne(Employee::class, 'id', 'manager_id');
    }

    public function workCenters()
    {
        return $this->hasMany(ProductionWorkCenter::class, 'industrial_resource_id');
    }
}
