<?php

namespace App\Services;

use App\Models\HotelDemandForecast;
use App\Models\HotelPriceRecommendation;
use App\Models\HotelPricingRule;
use App\Models\HotelRatePlan;
use App\Models\HotelRoomType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HotelPricingService
{
    public function generateRecommendations(?int $roomTypeId = null, ?string $date = null): Collection
    {
        $roomTypes = HotelRoomType::query()
            ->when($roomTypeId, fn ($query) => $query->whereKey($roomTypeId))
            ->get();

        $recommendations = collect();
        $creatorId = Auth::user()?->creatorId();

        foreach ($roomTypes as $roomType) {
            $ratePlans = HotelRatePlan::query()
                ->where('room_type_id', $roomType->id)
                ->get();

            $forecast = HotelDemandForecast::query()
                ->where('room_type_id', $roomType->id)
                ->when($date, fn ($query) => $query->whereDate('date', $date))
                ->orderByDesc('date')
                ->first();

            $rules = HotelPricingRule::query()
                ->where('is_active', true)
                ->where(function ($query) use ($roomType) {
                    $query->whereNull('room_type_id')
                        ->orWhere('room_type_id', $roomType->id);
                })
                ->get();

            foreach ($ratePlans as $ratePlan) {
                $baseRate = (float) $ratePlan->base_rate;
                $seasonality = $forecast ? (float) $forecast->seasonal_factor : 1;
                $eventFactor = $forecast ? (float) $forecast->event_factor : 1;
                $occupancy = $forecast ? (float) $forecast->occupancy_rate : 0;

                $multiplier = max($seasonality, 0.1) * max($eventFactor, 0.1);
                $ruleBoost = 0;
                $minRate = $ratePlan->min_rate > 0 ? (float) $ratePlan->min_rate : $baseRate * 0.7;
                $maxRate = $ratePlan->max_rate > 0 ? (float) $ratePlan->max_rate : $baseRate * 1.6;

                foreach ($rules as $rule) {
                    $ruleMin = (float) $rule->min_rate;
                    $ruleMax = (float) $rule->max_rate;
                    if ($ruleMin > 0) {
                        $minRate = max($minRate, $ruleMin);
                    }
                    if ($ruleMax > 0) {
                        $maxRate = min($maxRate, $ruleMax);
                    }
                    if ($occupancy >= (float) $rule->occupancy_threshold) {
                        $ruleBoost += (float) $rule->margin;
                        $multiplier *= max((float) $rule->seasonality_multiplier, 0.1);
                        $multiplier *= max((float) $rule->event_multiplier, 0.1);
                    }
                }

                $recommended = ($baseRate * $multiplier) + $ruleBoost;
                $recommended = max($minRate, min($maxRate, $recommended));

                $recommendations->push(HotelPriceRecommendation::create([
                    'room_type_id' => $roomType->id,
                    'rate_plan_id' => $ratePlan->id,
                    'date' => $date ?? now()->toDateString(),
                    'recommended_rate' => round($recommended, 2),
                    'reason' => $forecast ? 'forecast' : 'base',
                    'created_by' => $creatorId,
                ]));
            }
        }

        return $recommendations;
    }
}
