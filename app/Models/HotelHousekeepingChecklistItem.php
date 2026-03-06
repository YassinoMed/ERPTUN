<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelHousekeepingChecklistItem extends Model
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
