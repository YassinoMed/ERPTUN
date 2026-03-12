<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyUnit extends Model
{
    protected $fillable = [
        'managed_property_id',
        'unit_code',
        'floor',
        'area',
        'monthly_rent',
        'status',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'available' => 'Available',
        'occupied' => 'Occupied',
        'maintenance' => 'Maintenance',
        'reserved' => 'Reserved',
    ];

    public function property()
    {
        return $this->hasOne(ManagedProperty::class, 'id', 'managed_property_id');
    }

    public function leases()
    {
        return $this->hasMany(PropertyLease::class);
    }
}
