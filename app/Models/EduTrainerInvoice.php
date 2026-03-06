<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduTrainerInvoice extends Model
{
    protected $fillable = [
        'trainer_id',
        'period_start',
        'period_end',
        'total_hours',
        'total_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'id', 'trainer_id');
    }

    public function hours()
    {
        return $this->hasMany(EduTrainerHour::class, 'invoice_id');
    }
}
