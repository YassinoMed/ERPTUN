<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_code',
        'attendee_name',
        'attendee_email',
        'price',
        'status',
        'checked_in_at',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'reserved' => 'Reserved',
        'paid' => 'Paid',
        'checked_in' => 'Checked In',
        'cancelled' => 'Cancelled',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
