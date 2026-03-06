<?php

namespace App\Services;

use App\Models\AgriCoopMember;
use App\Models\AgriHarvestDelivery;
use App\Models\AgriMemberPayout;
use App\Models\AgriRevenueDistribution;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgriCooperativeService
{
    public function createDistribution(int $cooperativeId, string $periodStart, string $periodEnd, string $method = 'quantity'): AgriRevenueDistribution
    {
        $creatorId = Auth::user()?->creatorId();

        return DB::transaction(function () use ($cooperativeId, $periodStart, $periodEnd, $method, $creatorId) {
            $deliveries = AgriHarvestDelivery::query()
                ->where('cooperative_id', $cooperativeId)
                ->whereBetween('delivery_date', [$periodStart, $periodEnd])
                ->get();

            $totalRevenue = $deliveries->sum('total_value');
            $totalQuantity = $deliveries->sum('quantity');

            $distribution = AgriRevenueDistribution::create([
                'cooperative_id' => $cooperativeId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_revenue' => $totalRevenue,
                'distribution_method' => $method,
                'created_by' => $creatorId,
            ]);

            $deliveriesByMember = $deliveries->groupBy('member_id');

            foreach ($deliveriesByMember as $memberId => $memberDeliveries) {
                $member = AgriCoopMember::query()->find($memberId);
                if (!$member) {
                    continue;
                }

                $memberQuantity = $memberDeliveries->sum('quantity');
                $memberRevenue = $memberDeliveries->sum('total_value');

                if ($method === 'share' && $totalRevenue > 0) {
                    $memberRevenue = ($totalRevenue * ((float) $member->share_percent / 100));
                } elseif ($method === 'quantity' && $totalQuantity > 0) {
                    $memberRevenue = ($totalRevenue * ((float) $memberQuantity / (float) $totalQuantity));
                }

                $advanceDeducted = min((float) $member->advance_balance, (float) $memberRevenue);
                $netAmount = max(0, (float) $memberRevenue - $advanceDeducted);

                AgriMemberPayout::create([
                    'distribution_id' => $distribution->id,
                    'member_id' => $member->id,
                    'gross_amount' => $memberRevenue,
                    'advance_deducted' => $advanceDeducted,
                    'net_amount' => $netAmount,
                    'created_by' => $creatorId,
                ]);

                if ($advanceDeducted > 0) {
                    $member->update([
                        'advance_balance' => max(0, (float) $member->advance_balance - $advanceDeducted),
                    ]);
                }
            }

            return $distribution;
        });
    }
}
