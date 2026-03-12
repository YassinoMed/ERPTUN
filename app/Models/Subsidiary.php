<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subsidiary extends Model
{
    protected $fillable = [
        'name',
        'registration_number',
        'country',
        'currency',
        'ownership_percentage',
        'consolidation_method',
        'status',
        'parent_company',
        'notes',
        'created_by',
    ];

    public static $consolidationMethods = [
        'full' => 'Full',
        'equity' => 'Equity',
        'proportional' => 'Proportional',
    ];

    public static $statuses = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'disposed' => 'Disposed',
    ];
}
