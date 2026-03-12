<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorRating extends Model
{
    protected $fillable = [
        'vender_id',
        'period_label',
        'quality_score',
        'delivery_score',
        'cost_score',
        'service_score',
        'total_score',
        'status',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'approved' => 'Approved',
        'archived' => 'Archived',
    ];

    public function vender()
    {
        return $this->belongsTo(Vender::class);
    }
}
