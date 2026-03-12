<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedView extends Model
{
    protected $fillable = [
        'user_id',
        'module',
        'name',
        'filters',
        'columns',
        'sorts',
        'is_default',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'sorts' => 'array',
        'is_default' => 'boolean',
    ];
}
