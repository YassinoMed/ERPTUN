<?php

namespace App\Services;

use App\Models\AdvancedModuleFeatureState;
use App\Models\ModuleRecommendation;

class RecommendationApplier
{
    public function apply(ModuleRecommendation $recommendation): ModuleRecommendation
    {
        $recommendation->status = 'applied';
        $recommendation->applied_at = now();
        $recommendation->save();

        $relatedStates = AdvancedModuleFeatureState::query()
            ->where('owner_id', $recommendation->owner_id)
            ->where('module_key', $recommendation->module_key)
            ->get();

        foreach ($relatedStates as $state) {
            if ($state->status === 'planned') {
                $state->status = 'in_progress';
                $state->save();
            }
        }

        return $recommendation->fresh();
    }
}
