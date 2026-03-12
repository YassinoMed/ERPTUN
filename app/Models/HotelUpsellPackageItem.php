<?php

namespace App\Models;


class HotelUpsellPackageItem extends HotelModel
{
    protected $fillable = [
        'package_id',
        'service_id',
        'quantity',
    ];

    public function package()
    {
        return $this->belongsTo(HotelUpsellPackage::class, 'package_id');
    }

    public function service()
    {
        return $this->belongsTo(HotelUpsellService::class, 'service_id');
    }
}
