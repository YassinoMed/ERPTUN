<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $fillable = [
        'subject',
        'user',
        'configuration_item_id',
        'support_category_id',
        'priority',
        'end_date',
        'resolution_due_at',
        'resolved_at',
        'ticket_code',
        'ticket_created',
        'status',
        'is_internal',
        'ticket_type',
        'impact_level',
        'urgency_level',
        'created_by',
        'description',
    ];

    public static $priority = [
        'Low',
        'Medium',
        'High',
        'Critical',
    ];

    public function createdBy()
    {
        return $this->hasOne('App\Models\user', 'id', 'ticket_created');
    }

    public function assignUser()
    {
        return $this->hasOne('App\Models\user', 'id', 'user');
    }

    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'support_category_id');
    }

    public function configurationItem()
    {
        return $this->belongsTo(ConfigurationItem::class, 'configuration_item_id');
    }

    public static $status = [
        'Open' => 'Open',
        'Close' => 'Close',
        'On Hold' =>  'On Hold',
    ];

    public static function status() {
        $status['Open'] = __ ('Open');
        $status['Close'] = __ ('Close');
        $status['On Hold'] = __ ('On Hold');
        return $status;
    }

    public function replyUnread()
    {

        if(\Auth::user()->type == 'Employee')
        {
            return SupportReply:: where('support_id', $this->id)->where('is_read', 0)->where('user', '!=', \Auth::user()->id)->count('id');
        }
        else
        {
            return SupportReply:: where('support_id', $this->id)->where('is_read', 0)->count('id');
        }
    }
}
