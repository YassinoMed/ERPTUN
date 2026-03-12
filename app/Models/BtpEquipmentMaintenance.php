<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BtpEquipmentMaintenance extends BtpModel
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'scheduled_at',
        'completed_at',
        'cost',
        'note',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'date',
        'completed_at' => 'date',
        'cost' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->hasOne(BtpEquipment::class, 'id', 'equipment_id');
    }
}
