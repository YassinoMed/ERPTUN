<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientDocument extends Model
{
    protected $fillable = [
        'patient_id',
        'category',
        'title',
        'description',
        'file_path',
        'uploaded_at',
        'created_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
