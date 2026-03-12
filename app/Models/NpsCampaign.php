<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NpsCampaign extends Model
{
    protected $fillable = [
        'name',
        'channel',
        'status',
        'audience_type',
        'sent_at',
        'closes_at',
        'description',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'closed' => 'Closed',
    ];

    public function responses()
    {
        return $this->hasMany(NpsResponse::class)->latest('id');
    }
}
