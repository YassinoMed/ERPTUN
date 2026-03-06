<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpEquipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'status',
        'purchase_date',
        'rental_rate',
        'fuel_type',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'rental_rate' => 'decimal:2',
    ];

    public function usages()
    {
        return $this->hasMany(BtpEquipmentUsage::class, 'equipment_id');
    }

    public function maintenances()
    {
        return $this->hasMany(BtpEquipmentMaintenance::class, 'equipment_id');
    }
}
