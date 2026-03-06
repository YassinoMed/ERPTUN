<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduGrade extends Model
{
    protected $fillable = [
        'course_id',
        'employee_id',
        'score',
        'grade',
        'note',
        'created_by',
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
