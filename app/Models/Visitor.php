<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'visitor_name',
        'company_name',
        'email',
        'phone',
        'host_employee_id',
        'visit_date',
        'visit_time',
        'purpose',
        'status',
        'badge_number',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'expected' => 'Expected',
        'checked_in' => 'Checked In',
        'checked_out' => 'Checked Out',
        'cancelled' => 'Cancelled',
    ];

    public function host()
    {
        return $this->hasOne(Employee::class, 'id', 'host_employee_id');
    }
}
