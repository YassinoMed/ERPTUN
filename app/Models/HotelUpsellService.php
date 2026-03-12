<?php

namespace App\Models;


class HotelUpsellService extends HotelModel
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'stock',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function packageItems()
    {
        return $this->hasMany(HotelUpsellPackageItem::class, 'service_id');
    }
}
