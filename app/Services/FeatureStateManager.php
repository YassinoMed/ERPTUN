<?php

namespace App\Services;

use App\Models\AdvancedModuleFeatureState;

class FeatureStateManager
{
    public function __construct(private readonly AdvancedFeatureCatalog $catalog)
    {
    }

    public function listModuleStates(int $ownerId, string $module): array
    {
        $module = strtolower($module);

        return AdvancedModuleFeatureState::query()
            ->where('owner_id', $ownerId)
            ->where('module_key', $module)
            ->orderBy('feature_key')
            ->get()
            ->all();
    }

    public function updateState(int $ownerId, string $module, string $featureKey, array $attributes): AdvancedModuleFeatureState
    {
        $module = strtolower($module);

        return AdvancedModuleFeatureState::query()->updateOrCreate(
            [
                'owner_id' => $ownerId,
                'module_key' => $module,
                'feature_key' => $featureKey,
            ],
            [
                'status' => $attributes['status'] ?? 'planned',
                'priority' => $attributes['priority'] ?? ($this->catalog->module($module)['priority'] ?? 'medium'),
                'meta' => array_filter([
                    'name' => $attributes['name'] ?? null,
                    'notes' => $attributes['notes'] ?? null,
                    'updated_at' => now()->toIso8601String(),
                ]),
            ]
        );
    }

    public function activateAllModuleFeatures(int $ownerId, string $module): int
    {
        return AdvancedModuleFeatureState::query()
            ->where('owner_id', $ownerId)
            ->where('module_key', strtolower($module))
            ->update(['status' => 'active']);
    }
}
