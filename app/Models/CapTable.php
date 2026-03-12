<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapTable extends Model
{
    protected $fillable = [
        'holder_name',
        'holder_type',
        'share_class',
        'share_count',
        'issue_price',
        'ownership_percentage',
        'voting_percentage',
        'contact_email',
        'contact_phone',
        'notes',
        'created_by',
    ];

    public static $holderTypes = [
        'individual' => 'Individual',
        'company' => 'Company',
        'fund' => 'Fund',
        'employee' => 'Employee',
    ];
}
