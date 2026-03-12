<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineEntry extends Model
{
    protected $fillable = [
        'created_by',
        'user_id',
        'timelineable_type',
        'timelineable_id',
        'entry_type',
        'title',
        'body',
        'metadata',
        'happened_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'happened_at' => 'datetime',
    ];

    public function timelineable()
    {
        return $this->morphTo();
    }
}
