<?php

namespace Database\Seeders\Modules;

class TraceabilitySeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Traceability';

    protected function seed(): void
    {
        if ($this->tableExists('traceability_statuses')) {
            $statuses = [
                ['name' => 'Registered', 'slug' => 'registered', 'color' => '#3498DB'],
                ['name' => 'In Transit', 'slug' => 'in_transit', 'color' => '#F39C12'],
                ['name' => 'Received', 'slug' => 'received', 'color' => '#27AE60'],
                ['name' => 'In Storage', 'slug' => 'in_storage', 'color' => '#9B59B6'],
                ['name' => 'In Processing', 'slug' => 'in_processing', 'color' => '#E67E22'],
                ['name' => 'Dispatched', 'slug' => 'dispatched', 'color' => '#1ABC9C'],
                ['name' => 'Recalled', 'slug' => 'recalled', 'color' => '#E74C3C'],
            ];
            foreach ($statuses as $s) {
                $this->upsert('traceability_statuses', ['slug' => $s['slug']], $s);
            }
        }

        if ($this->tableExists('trace_event_types')) {
            $types = [
                ['name' => 'Harvest', 'slug' => 'harvest'],
                ['name' => 'Transport', 'slug' => 'transport'],
                ['name' => 'Storage', 'slug' => 'storage'],
                ['name' => 'Processing', 'slug' => 'processing'],
                ['name' => 'Quality Test', 'slug' => 'quality_test'],
                ['name' => 'Packaging', 'slug' => 'packaging'],
                ['name' => 'Distribution', 'slug' => 'distribution'],
                ['name' => 'Recall', 'slug' => 'recall'],
            ];
            foreach ($types as $t) {
                $this->upsert('trace_event_types', ['slug' => $t['slug']], $t);
            }
        }
    }
}
