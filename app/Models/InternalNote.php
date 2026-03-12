<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalNote extends Model
{
    protected $fillable = [
        'created_by',
        'user_id',
        'notable_type',
        'notable_id',
        'body',
        'is_pinned',
        'visibility',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function notable()
    {
        return $this->morphTo();
    }
}
