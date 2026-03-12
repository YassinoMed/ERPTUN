<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalBed extends Model
{
    protected $fillable = [
        'hospital_room_id',
        'bed_number',
        'status',
        'created_by',
    ];

    public function room()
    {
        return $this->belongsTo(HospitalRoom::class, 'hospital_room_id');
    }
}
