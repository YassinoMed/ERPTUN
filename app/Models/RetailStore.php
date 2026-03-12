<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailStore extends Model
{
    protected $fillable = [
        'name',
        'code',
        'store_type',
        'region',
        'manager_name',
        'parent_store_id',
        'warehouse_id',
        'target_revenue',
        'target_margin',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'target_revenue' => 'decimal:2',
        'target_margin' => 'decimal:2',
    ];

    public function posSessions()
    {
        return $this->hasMany(PosSession::class);
    }

    public function parentStore()
    {
        return $this->belongsTo(self::class, 'parent_store_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_store_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
