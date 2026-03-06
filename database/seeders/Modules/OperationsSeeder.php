<?php

namespace Database\Seeders\Modules;

/**
 * Operations Service Seeder
 * Seeds: operation statuses, priority levels, operation categories
 */
class OperationsSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Operations';

    protected function seed(): void
    {
        if ($this->tableExists('operation_statuses')) {
            $statuses = [
                ['name' => 'Planned', 'slug' => 'planned', 'color' => '#3498DB', 'sort_order' => 1],
                ['name' => 'In Progress', 'slug' => 'in_progress', 'color' => '#F39C12', 'sort_order' => 2],
                ['name' => 'On Hold', 'slug' => 'on_hold', 'color' => '#E67E22', 'sort_order' => 3],
                ['name' => 'Completed', 'slug' => 'completed', 'color' => '#27AE60', 'sort_order' => 4],
                ['name' => 'Cancelled', 'slug' => 'cancelled', 'color' => '#E74C3C', 'sort_order' => 5],
            ];
            foreach ($statuses as $s) {
                $this->upsert('operation_statuses', ['slug' => $s['slug']], $s);
            }
        }

        if ($this->tableExists('priority_levels')) {
            $priorities = [
                ['name' => 'Critical', 'level' => 1, 'color' => '#E74C3C'],
                ['name' => 'High', 'level' => 2, 'color' => '#E67E22'],
                ['name' => 'Medium', 'level' => 3, 'color' => '#F39C12'],
                ['name' => 'Low', 'level' => 4, 'color' => '#27AE60'],
                ['name' => 'Minimal', 'level' => 5, 'color' => '#95A5A6'],
            ];
            foreach ($priorities as $p) {
                $this->upsert('priority_levels', ['level' => $p['level']], $p);
            }
        }

        if ($this->tableExists('operation_categories')) {
            $categories = [
                ['name' => 'Logistics', 'slug' => 'logistics'],
                ['name' => 'Procurement', 'slug' => 'procurement'],
                ['name' => 'Supply Chain', 'slug' => 'supply_chain'],
                ['name' => 'Warehousing', 'slug' => 'warehousing'],
                ['name' => 'Distribution', 'slug' => 'distribution'],
                ['name' => 'Quality Control', 'slug' => 'quality_control'],
            ];
            foreach ($categories as $c) {
                $this->upsert('operation_categories', ['slug' => $c['slug']], $c);
            }
        }
    }
}
