<?php

namespace App\Services;

use App\Models\AgriHedgePosition;
use App\Models\AgriPriceIndex;
use App\Models\AgriPurchaseContract;
use Illuminate\Support\Facades\Auth;

class AgriHedgingService
{
    public function createHedgePosition(AgriPurchaseContract $contract, array $payload): AgriHedgePosition
    {
        $creatorId = Auth::user()?->creatorId();
        $ratio = isset($payload['hedge_ratio']) ? (float) $payload['hedge_ratio'] : (float) $contract->hedge_ratio;
        $ratio = $ratio > 0 ? $ratio : 0;
        $quantity = (float) $contract->quantity * ($ratio / 100);

        $latestPrice = AgriPriceIndex::query()
            ->where('crop_type', $contract->crop_type)
            ->latest('recorded_at')
            ->value('price');

        return AgriHedgePosition::create([
            'contract_id' => $contract->id,
            'instrument' => $payload['instrument'] ?? 'FUT',
            'position_type' => $payload['position_type'] ?? 'future',
            'quantity' => $payload['quantity'] ?? $quantity,
            'price' => $payload['price'] ?? ($latestPrice ?? $contract->fixed_price),
            'opened_at' => $payload['opened_at'] ?? now()->toDateString(),
            'closed_at' => $payload['closed_at'] ?? null,
            'status' => $payload['status'] ?? 'open',
            'created_by' => $creatorId,
        ]);
    }
}
