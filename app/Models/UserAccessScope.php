<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccessScope extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'scope_type',
        'scope_id',
        'assigned_by',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLabel(): string
    {
        return sprintf('%s #%d', ucfirst($this->scope_type), $this->scope_id);
    }
}
