<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtpEquipmentUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'project_id',
        'start_date',
        'end_date',
        'hours_used',
        'fuel_consumed',
        'note',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hours_used' => 'decimal:2',
        'fuel_consumed' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->hasOne(BtpEquipment::class, 'id', 'equipment_id');
    }
}
