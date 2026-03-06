<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelUpsellPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(HotelUpsellPackageItem::class, 'package_id');
    }
}
