<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduAttendance extends Model
{
    protected $fillable = [
        'session_id',
        'employee_id',
        'status',
        'note',
        'created_by',
    ];

    public function session()
    {
        return $this->hasOne(EduSession::class, 'id', 'session_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
