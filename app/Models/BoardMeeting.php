<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardMeeting extends Model
{
    protected $fillable = [
        'branch_id',
        'title',
        'meeting_date',
        'meeting_time',
        'status',
        'location',
        'meeting_link',
        'agenda',
        'minutes',
        'resolution_summary',
        'external_guests',
        'created_by',
    ];

    public static $status = [
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public static function statuses()
    {
        return [
            'scheduled' => __('Scheduled'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
        ];
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function attendees()
    {
        return $this->hasMany(BoardMeetingEmployee::class, 'board_meeting_id');
    }
}
