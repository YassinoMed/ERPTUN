<?php

namespace App\Models;


class HotelHousekeepingChecklistItem extends HotelModel
{
    protected $fillable = [
        'checklist_id',
        'title',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function checklist()
    {
        return $this->belongsTo(HotelHousekeepingChecklist::class, 'checklist_id');
    }
}
