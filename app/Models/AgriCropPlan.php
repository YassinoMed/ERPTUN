<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriCropPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'crop_name',
        'variety',
        'start_date',
        'end_date',
        'status',
        'expected_yield',
        'yield_unit',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expected_yield' => 'decimal:3',
    ];

    public function parcel()
    {
        return $this->belongsTo(AgriParcel::class, 'parcel_id');
    }
}
