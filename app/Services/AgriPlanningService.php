<?php

namespace App\Services;

use App\Models\AgriCropPlan;
use App\Models\AgriRotationRule;
use Illuminate\Support\Collection;

class AgriPlanningService
{
    public function getRotationRecommendations(int $parcelId): Collection
    {
        $lastPlan = AgriCropPlan::query()
            ->where('parcel_id', $parcelId)
            ->latest('end_date')
            ->first();

        if (!$lastPlan) {
            return collect();
        }

        return AgriRotationRule::query()
            ->where('crop_name', $lastPlan->crop_name)
            ->orderByDesc('min_gap_days')
            ->get()
            ->map(fn ($rule) => [
                'crop_name' => $rule->crop_name,
                'follow_crop_name' => $rule->follow_crop_name,
                'min_gap_days' => $rule->min_gap_days,
                'recommendation' => $rule->recommendation,
            ]);
    }
}
