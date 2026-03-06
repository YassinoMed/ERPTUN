<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduCourseModule extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'content_url',
        'duration_minutes',
        'sort_order',
        'created_by',
    ];

    public function course()
    {
        return $this->hasOne(EduCourse::class, 'id', 'course_id');
    }
}
