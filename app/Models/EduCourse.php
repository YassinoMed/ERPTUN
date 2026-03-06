<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduCourse extends Model
{
    protected $fillable = [
        'code',
        'name',
        'training_type_id',
        'trainer_id',
        'delivery_mode',
        'start_date',
        'end_date',
        'description',
        'created_by',
    ];

    public function trainingType()
    {
        return $this->hasOne(TrainingType::class, 'id', 'training_type_id');
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'id', 'trainer_id');
    }

    public function modules()
    {
        return $this->hasMany(EduCourseModule::class, 'course_id');
    }

    public function sessions()
    {
        return $this->hasMany(EduSession::class, 'course_id');
    }

    public function enrollments()
    {
        return $this->hasMany(EduEnrollment::class, 'course_id');
    }
}
