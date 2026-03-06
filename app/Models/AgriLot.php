<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'crop_type',
        'harvest_date',
        'quantity',
        'unit',
        'status',
        'created_by',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'quantity' => 'decimal:3',
    ];

    public function traceEvents()
    {
        return $this->hasMany(AgriTraceEvent::class, 'lot_id');
    }

    public function certificate()
    {
        return $this->hasOne(AgriCertificate::class, 'lot_id');
    }
}
