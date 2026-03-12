<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionShiftTeam extends ProductionModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'supervisor_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'created_by',
    ];

    public function supervisor()
    {
        return $this->hasOne(Employee::class, 'id', 'supervisor_id');
    }

    public function orders()
    {
        return $this->hasMany(ProductionOrder::class, 'production_shift_team_id');
    }
}
