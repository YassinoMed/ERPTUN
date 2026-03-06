<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriTraceEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'step',
        'location',
        'actor',
        'notes',
        'occurred_at',
        'previous_hash',
        'current_hash',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function lot()
    {
        return $this->belongsTo(AgriLot::class, 'lot_id');
    }
}
