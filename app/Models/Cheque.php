<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $fillable = [
        'beneficiary_name',
        'amount',
        'amount_text',
        'currency',
        'issue_date',
        'due_date',
        'bank_name',
        'bank_agency',
        'bank_account',
        'rib',
        'chequebook_number',
        'cheque_number',
        'status',
        'status_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'status_date' => 'date',
    ];
}
