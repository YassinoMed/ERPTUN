<?php

namespace App\Services;

class AdvancedFeatureCatalog
{
    public function allModules(): array
    {
        return config('advanced_features.modules', []);
    }

    public function module(string $module): ?array
    {
        return $this->allModules()[strtolower($module)] ?? null;
    }

    public function keys(): array
    {
        return array_keys($this->allModules());
    }

    public function roadmap(): array
    {
        return config('advanced_features.roadmap', []);
    }

    public function workflowCatalog(): array
    {
        return config('advanced_features.workflow', []);
    }

    public function prioritizedModules(): array
    {
        $modules = $this->allModules();

        uasort($modules, static function (array $left, array $right): int {
            $priorityWeight = ['high' => 0, 'medium' => 1, 'low' => 2];

            return ($priorityWeight[$left['priority'] ?? 'low'] ?? 3) <=> ($priorityWeight[$right['priority'] ?? 'low'] ?? 3);
        });

        return $modules;
    }
}
