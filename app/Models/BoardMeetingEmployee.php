<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardMeetingEmployee extends Model
{
    protected $fillable = [
        'board_meeting_id',
        'employee_id',
        'created_by',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function boardMeeting()
    {
        return $this->belongsTo(BoardMeeting::class, 'board_meeting_id');
    }
}
