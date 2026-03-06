<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduSession extends Model
{
    protected $fillable = [
        'course_id',
        'scheduled_at',
        'duration_hours',
        'location',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function course()
    {
        return $this->hasOne(EduCourse::class, 'id', 'course_id');
    }

    public function attendances()
    {
        return $this->hasMany(EduAttendance::class, 'session_id');
    }
}
