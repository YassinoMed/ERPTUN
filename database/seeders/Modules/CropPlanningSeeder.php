<?php

namespace Database\Seeders\Modules;

class CropPlanningSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'CropPlanning';

    protected function seed(): void
    {
        if ($this->tableExists('crop_types')) {
            $crops = [
                ['name' => 'Blé', 'slug' => 'wheat', 'season' => 'winter', 'cycle_days' => 240],
                ['name' => 'Maïs', 'slug' => 'corn', 'season' => 'spring', 'cycle_days' => 120],
                ['name' => 'Riz', 'slug' => 'rice', 'season' => 'spring', 'cycle_days' => 150],
                ['name' => 'Tomate', 'slug' => 'tomato', 'season' => 'spring', 'cycle_days' => 90],
                ['name' => 'Pomme de terre', 'slug' => 'potato', 'season' => 'spring', 'cycle_days' => 110],
                ['name' => 'Olive', 'slug' => 'olive', 'season' => 'perennial', 'cycle_days' => 365],
                ['name' => 'Agrumes', 'slug' => 'citrus', 'season' => 'perennial', 'cycle_days' => 365],
                ['name' => 'Argan', 'slug' => 'argan', 'season' => 'perennial', 'cycle_days' => 365],
            ];
            foreach ($crops as $c) {
                $this->upsert('crop_types', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('soil_types')) {
            $soils = [
                ['name' => 'Argile', 'slug' => 'clay', 'ph_range' => '6.0-7.5'],
                ['name' => 'Sable', 'slug' => 'sandy', 'ph_range' => '5.5-7.0'],
                ['name' => 'Limon', 'slug' => 'silt', 'ph_range' => '6.0-7.5'],
                ['name' => 'Terreau', 'slug' => 'loam', 'ph_range' => '6.0-7.0'],
                ['name' => 'Calcaire', 'slug' => 'calcareous', 'ph_range' => '7.0-8.5'],
            ];
            foreach ($soils as $s) {
                $this->upsert('soil_types', ['slug' => $s['slug']], $s);
            }
        }

        if ($this->tableExists('planning_statuses')) {
            $statuses = [
                ['name' => 'Planifié', 'slug' => 'planned', 'color' => '#3498DB'],
                ['name' => 'Semé', 'slug' => 'sowed', 'color' => '#8E44AD'],
                ['name' => 'En croissance', 'slug' => 'growing', 'color' => '#27AE60'],
                ['name' => 'Récolté', 'slug' => 'harvested', 'color' => '#F39C12'],
                ['name' => 'Stocké', 'slug' => 'stored', 'color' => '#1ABC9C'],
            ];
            foreach ($statuses as $s) {
                $this->upsert('planning_statuses', ['slug' => $s['slug']], $s);
            }
        }
    }
}
