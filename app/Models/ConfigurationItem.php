<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurationItem extends Model
{
    protected $fillable = [
        'name',
        'ci_type',
        'status',
        'criticality',
        'asset_id',
        'owner_user_id',
        'asset_tag',
        'serial_number',
        'location',
        'environment',
        'vendor_name',
        'acquired_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'acquired_at' => 'date',
    ];

    public static $statuses = [
        'active' => 'Active',
        'maintenance' => 'Maintenance',
        'retired' => 'Retired',
    ];

    public static $criticalities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
