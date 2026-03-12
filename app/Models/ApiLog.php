<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'api_client_id',
        'user_id',
        'route',
        'method',
        'status_code',
        'request_payload',
        'response_payload',
        'ip_address',
        'user_agent',
        'requested_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'requested_at' => 'datetime',
    ];

    public $timestamps = false;

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
