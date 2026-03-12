<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimsRecord extends Model
{
    protected $fillable = [
        'sample_code',
        'product_service_id',
        'lot_reference',
        'test_type',
        'status',
        'result_summary',
        'tested_at',
        'approved_by',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'scheduled' => 'Scheduled',
        'in_progress' => 'In Progress',
        'validated' => 'Validated',
        'rejected' => 'Rejected',
    ];

    public function productService()
    {
        return $this->belongsTo(ProductService::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
