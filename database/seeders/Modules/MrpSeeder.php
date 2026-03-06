<?php

namespace Database\Seeders\Modules;

/**
 * MRP Service Seeder — Manufacturing Resource Planning
 * Seeds: production statuses, resource types, BOM categories, unit measures
 */
class MrpSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'MRP';

    protected function seed(): void
    {
        if ($this->tableExists('production_statuses')) {
            $statuses = [
                ['name' => 'Draft', 'slug' => 'draft', 'color' => '#BDC3C7', 'sort_order' => 1],
                ['name' => 'Planned', 'slug' => 'planned', 'color' => '#3498DB', 'sort_order' => 2],
                ['name' => 'In Production', 'slug' => 'in_production', 'color' => '#F39C12', 'sort_order' => 3],
                ['name' => 'Quality Check', 'slug' => 'quality_check', 'color' => '#9B59B6', 'sort_order' => 4],
                ['name' => 'Completed', 'slug' => 'completed', 'color' => '#27AE60', 'sort_order' => 5],
                ['name' => 'On Hold', 'slug' => 'on_hold', 'color' => '#E67E22', 'sort_order' => 6],
                ['name' => 'Cancelled', 'slug' => 'cancelled', 'color' => '#E74C3C', 'sort_order' => 7],
            ];
            foreach ($statuses as $s) {
                $this->upsert('production_statuses', ['slug' => $s['slug']], $s);
            }
        }

        if ($this->tableExists('resource_types')) {
            $types = [
                ['name' => 'Machine', 'slug' => 'machine', 'unit' => 'hour'],
                ['name' => 'Labour', 'slug' => 'labour', 'unit' => 'hour'],
                ['name' => 'Material', 'slug' => 'material', 'unit' => 'unit'],
                ['name' => 'Tool', 'slug' => 'tool', 'unit' => 'piece'],
                ['name' => 'Energy', 'slug' => 'energy', 'unit' => 'kwh'],
            ];
            foreach ($types as $t) {
                $this->upsert('resource_types', ['slug' => $t['slug']], $t);
            }
        }

        if ($this->tableExists('unit_measures')) {
            $units = [
                ['name' => 'Piece', 'abbreviation' => 'pcs', 'type' => 'quantity'],
                ['name' => 'Kilogram', 'abbreviation' => 'kg', 'type' => 'weight'],
                ['name' => 'Gram', 'abbreviation' => 'g', 'type' => 'weight'],
                ['name' => 'Liter', 'abbreviation' => 'L', 'type' => 'volume'],
                ['name' => 'Meter', 'abbreviation' => 'm', 'type' => 'length'],
                ['name' => 'Square Meter', 'abbreviation' => 'm²', 'type' => 'area'],
                ['name' => 'Hour', 'abbreviation' => 'hr', 'type' => 'time'],
                ['name' => 'Box', 'abbreviation' => 'box', 'type' => 'packaging'],
                ['name' => 'Pallet', 'abbreviation' => 'pallet', 'type' => 'packaging'],
            ];
            foreach ($units as $u) {
                $this->upsert('unit_measures', ['abbreviation' => $u['abbreviation']], $u);
            }
        }
    }
}
