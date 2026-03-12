<?php

namespace App\Models;


class HotelHousekeepingTaskItem extends HotelModel
{
    protected $fillable = [
        'task_id',
        'checklist_item_id',
        'is_done',
        'notes',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(HotelHousekeepingTask::class, 'task_id');
    }

    public function checklistItem()
    {
        return $this->belongsTo(HotelHousekeepingChecklistItem::class, 'checklist_item_id');
    }
}
