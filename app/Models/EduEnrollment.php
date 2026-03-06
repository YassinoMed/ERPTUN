<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'employee_id',
        'status',
        'enrolled_at',
        'completed_at',
        'progress_percent',
        'created_by',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
    ];

    public function course()
    {
        return $this->hasOne(EduCourse::class, 'id', 'course_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
