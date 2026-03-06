<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduTrainerHour extends Model
{
    protected $fillable = [
        'trainer_id',
        'course_id',
        'session_id',
        'invoice_id',
        'hours',
        'rate',
        'amount',
        'declared_at',
        'note',
        'created_by',
    ];

    protected $casts = [
        'declared_at' => 'date',
    ];

    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'id', 'trainer_id');
    }

    public function course()
    {
        return $this->hasOne(EduCourse::class, 'id', 'course_id');
    }

    public function session()
    {
        return $this->hasOne(EduSession::class, 'id', 'session_id');
    }
}
