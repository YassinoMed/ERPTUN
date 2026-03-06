<?php

namespace App\Services;

use App\Models\HotelUpsellOffer;
use App\Models\HotelUpsellPackage;
use App\Models\HotelUpsellService;
use Illuminate\Support\Facades\Auth;

class HotelUpsellSuggestionService
{
    public function generateOffer(array $criteria = []): HotelUpsellOffer
    {
        $services = HotelUpsellService::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->limit(6)
            ->get();

        $packages = HotelUpsellPackage::query()
            ->where('is_active', true)
            ->with('items.service')
            ->limit(4)
            ->get();

        $payload = [
            'services' => $services->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'currency' => $service->currency,
            ])->values(),
            'packages' => $packages->map(fn ($package) => [
                'id' => $package->id,
                'name' => $package->name,
                'items' => $package->items->map(fn ($item) => [
                    'service_id' => $item->service_id,
                    'name' => $item->service?->name,
                    'quantity' => $item->quantity,
                ])->values(),
            ])->values(),
        ];

        return HotelUpsellOffer::create([
            'reservation_id' => $criteria['reservation_id'] ?? null,
            'customer_id' => $criteria['customer_id'] ?? null,
            'room_type_id' => $criteria['room_type_id'] ?? null,
            'stay_length_min' => $criteria['stay_length_min'] ?? null,
            'stay_length_max' => $criteria['stay_length_max'] ?? null,
            'segments' => $criteria['segments'] ?? [],
            'offer_payload' => $payload,
            'status' => 'proposed',
            'created_by' => Auth::user()?->creatorId(),
        ]);
    }
}
