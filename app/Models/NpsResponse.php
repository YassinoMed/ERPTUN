<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NpsResponse extends Model
{
    protected $fillable = [
        'nps_campaign_id',
        'customer_id',
        'score',
        'sentiment',
        'feedback',
        'responded_at',
        'created_by',
    ];

    public function campaign()
    {
        return $this->belongsTo(NpsCampaign::class, 'nps_campaign_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
