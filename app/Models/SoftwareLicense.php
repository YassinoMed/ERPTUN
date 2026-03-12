<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    protected $fillable = [
        'name',
        'vendor_name',
        'license_key',
        'license_type',
        'status',
        'configuration_item_id',
        'assigned_user_id',
        'seats_total',
        'seats_used',
        'cost',
        'renewal_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'renewal_date' => 'date',
    ];

    public static $statuses = [
        'active' => 'Active',
        'expiring' => 'Expiring',
        'expired' => 'Expired',
        'revoked' => 'Revoked',
    ];

    public function configurationItem()
    {
        return $this->belongsTo(ConfigurationItem::class, 'configuration_item_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
