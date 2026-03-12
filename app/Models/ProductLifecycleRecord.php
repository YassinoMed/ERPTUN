<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLifecycleRecord extends Model
{
    protected $fillable = [
        'product_service_id',
        'stage',
        'status',
        'effective_date',
        'owner_employee_id',
        'compliance_status',
        'notes',
        'created_by',
    ];

    public static $stages = [
        'concept' => 'Concept',
        'development' => 'Development',
        'validation' => 'Validation',
        'release' => 'Release',
        'retirement' => 'Retirement',
    ];

    public static $statuses = [
        'planned' => 'Planned',
        'active' => 'Active',
        'blocked' => 'Blocked',
        'completed' => 'Completed',
    ];

    public function productService()
    {
        return $this->belongsTo(ProductService::class);
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner_employee_id');
    }
}
