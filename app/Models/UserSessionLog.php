<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSessionLog extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'session_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'last_seen_at',
        'is_active',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
