<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduCertificate extends Model
{
    protected $fillable = [
        'enrollment_id',
        'certificate_number',
        'issued_at',
        'verification_hash',
        'qr_payload',
        'status',
        'sent_to_email',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->hasOne(EduEnrollment::class, 'id', 'enrollment_id');
    }
}
