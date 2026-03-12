<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordAccessLog extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'action',
        'context',
        'ip_address',
        'user_agent',
        'created_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($patientId, $action, $context = null)
    {
        if (!\Auth::check()) {
            return;
        }

        static::create([
            'patient_id' => $patientId,
            'user_id' => \Auth::id(),
            'action' => $action,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_by' => \Auth::user()->creatorId(),
        ]);
    }
}
