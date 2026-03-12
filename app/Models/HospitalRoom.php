<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalRoom extends Model
{
    protected $fillable = [
        'name',
        'department',
        'room_type',
        'status',
        'created_by',
    ];

    public function beds()
    {
        return $this->hasMany(HospitalBed::class)->orderBy('bed_number');
    }
}
