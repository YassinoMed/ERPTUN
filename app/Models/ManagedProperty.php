<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagedProperty extends Model
{
    protected $fillable = [
        'name',
        'property_code',
        'property_type',
        'status',
        'manager_employee_id',
        'country',
        'city',
        'address',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'maintenance' => 'Maintenance',
        'inactive' => 'Inactive',
    ];

    public function manager()
    {
        return $this->hasOne(Employee::class, 'id', 'manager_employee_id');
    }

    public function units()
    {
        return $this->hasMany(PropertyUnit::class);
    }

    public function leases()
    {
        return $this->hasMany(PropertyLease::class);
    }
}
