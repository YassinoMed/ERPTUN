<?php

namespace App\Services;

use App\Models\AnalyticsDashboard;
use App\Models\AnalyticsWidget;

class AnalyticsDashboardProvisioner
{
    public function __construct(private readonly AdvancedFeatureCatalog $catalog)
    {
    }

    public function provisionForOwner(int $ownerId): array
    {
        $dashboard = AnalyticsDashboard::query()->updateOrCreate(
            [
                'created_by' => $ownerId,
                'name' => 'Advanced ERP Cockpit',
            ],
            [
                'description' => 'Cross-module KPI cockpit generated from the advanced feature catalog.',
                'filters' => [
                    'scope' => 'owner',
                    'owner_id' => $ownerId,
                ],
            ]
        );

        $widgets = [];
        $position = 0;

        foreach ($this->catalog->prioritizedModules() as $moduleKey => $definition) {
            $widget = AnalyticsWidget::query()
                ->where('dashboard_id', $dashboard->id)
                ->where('type', 'module_kpi_overview')
                ->where('created_by', $ownerId)
                ->where('config->module_key', $moduleKey)
                ->first();

            if (! $widget) {
                $widget = new AnalyticsWidget([
                    'dashboard_id' => $dashboard->id,
                    'type' => 'module_kpi_overview',
                    'created_by' => $ownerId,
                ]);
            }

            $widget->config = [
                'module_key' => $moduleKey,
                'module_name' => $definition['name'],
                'kpis' => $definition['kpis'] ?? [],
                'priority' => $definition['priority'] ?? 'medium',
            ];
            $widget->position = [
                'x' => $position % 2,
                'y' => intdiv($position, 2),
                'w' => 1,
                'h' => 1,
            ];
            $widget->save();

            $widgets[] = $widget->id;

            $position++;
        }

        return [
            'owner_id' => $ownerId,
            'dashboard_id' => $dashboard->id,
            'widget_count' => count($widgets),
        ];
    }
}
