<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'in_app',
        'email',
        'sms',
        'whatsapp',
        'created_by',
    ];

    protected $casts = [
        'in_app' => 'boolean',
        'email' => 'boolean',
        'sms' => 'boolean',
        'whatsapp' => 'boolean',
    ];
}
