<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'certificate_number',
        'issued_at',
        'verification_hash',
        'qr_payload',
        'status',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }
}
