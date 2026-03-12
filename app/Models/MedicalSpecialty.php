<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalSpecialty extends Model
{
    protected $fillable = [
        'name',
        'code',
        'department_name',
        'head_name',
        'status',
        'notes',
        'created_by',
    ];
}
