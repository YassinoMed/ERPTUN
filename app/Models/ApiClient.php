<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'client_key',
        'client_secret',
        'abilities',
        'is_active',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(ApiLog::class);
    }
}
