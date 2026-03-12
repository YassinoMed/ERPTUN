<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdprProcessingActivity extends Model
{
    protected $fillable = [
        'activity_name',
        'activity_code',
        'data_category',
        'purpose',
        'lawful_basis',
        'processor_name',
        'retention_period',
        'status',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'active' => 'Active',
        'review' => 'In Review',
        'retired' => 'Retired',
    ];
}
