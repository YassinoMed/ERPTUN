<?php

namespace Database\Seeders\Modules;

/**
 * BTP Service Seeder — Construction / Bâtiment et Travaux Publics
 * Seeds: site statuses, equipment types, work phases, subcontractor categories
 */
class BtpSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'BTP';

    protected function seed(): void
    {
        if ($this->tableExists('site_statuses')) {
            $statuses = [
                ['name' => 'Étude', 'slug' => 'study', 'color' => '#3498DB', 'sort_order' => 1],
                ['name' => 'Permis en cours', 'slug' => 'permit_pending', 'color' => '#F39C12', 'sort_order' => 2],
                ['name' => 'Travaux préparatoires', 'slug' => 'preparation', 'color' => '#E67E22', 'sort_order' => 3],
                ['name' => 'En construction', 'slug' => 'construction', 'color' => '#27AE60', 'sort_order' => 4],
                ['name' => 'Finitions', 'slug' => 'finishing', 'color' => '#9B59B6', 'sort_order' => 5],
                ['name' => 'Réception', 'slug' => 'reception', 'color' => '#2ECC71', 'sort_order' => 6],
                ['name' => 'Achevé', 'slug' => 'completed', 'color' => '#1ABC9C', 'sort_order' => 7],
                ['name' => 'Suspendu', 'slug' => 'suspended', 'color' => '#E74C3C', 'sort_order' => 8],
            ];
            foreach ($statuses as $s) {
                $this->upsert('site_statuses', ['slug' => $s['slug']], $s);
            }
        }

        if ($this->tableExists('equipment_types')) {
            $types = [
                ['name' => 'Grue', 'slug' => 'crane', 'category' => 'heavy_machinery'],
                ['name' => 'Pelle mécanique', 'slug' => 'excavator', 'category' => 'heavy_machinery'],
                ['name' => 'Bétonnière', 'slug' => 'concrete_mixer', 'category' => 'concrete'],
                ['name' => 'Chariot élévateur', 'slug' => 'forklift', 'category' => 'transport'],
                ['name' => 'Échafaudage', 'slug' => 'scaffolding', 'category' => 'structure'],
                ['name' => 'Compacteur', 'slug' => 'compactor', 'category' => 'earthworks'],
                ['name' => 'Bulldozer', 'slug' => 'bulldozer', 'category' => 'earthworks'],
                ['name' => 'Camion benne', 'slug' => 'dump_truck', 'category' => 'transport'],
            ];
            foreach ($types as $t) {
                $this->upsert('equipment_types', ['slug' => $t['slug']], $t);
            }
        }

        if ($this->tableExists('work_phases')) {
            $phases = [
                ['name' => 'Terrassement', 'slug' => 'earthworks', 'sort_order' => 1],
                ['name' => 'Fondations', 'slug' => 'foundations', 'sort_order' => 2],
                ['name' => 'Gros Œuvre', 'slug' => 'structural_work', 'sort_order' => 3],
                ['name' => 'Charpente / Toiture', 'slug' => 'roofing', 'sort_order' => 4],
                ['name' => 'Second Œuvre', 'slug' => 'secondary_work', 'sort_order' => 5],
                ['name' => 'Électricité', 'slug' => 'electrical', 'sort_order' => 6],
                ['name' => 'Plomberie', 'slug' => 'plumbing', 'sort_order' => 7],
                ['name' => 'Peinture / Finitions', 'slug' => 'finishing', 'sort_order' => 8],
                ['name' => 'Aménagement extérieur', 'slug' => 'landscaping', 'sort_order' => 9],
            ];
            foreach ($phases as $p) {
                $this->upsert('work_phases', ['slug' => $p['slug']], $p);
            }
        }

        if ($this->tableExists('subcontractor_categories')) {
            $categories = [
                ['name' => 'Maçonnerie', 'slug' => 'masonry'],
                ['name' => 'Électricité', 'slug' => 'electricity'],
                ['name' => 'Plomberie', 'slug' => 'plumbing'],
                ['name' => 'Peinture', 'slug' => 'painting'],
                ['name' => 'Menuiserie', 'slug' => 'carpentry'],
                ['name' => 'Carrelage', 'slug' => 'tiling'],
                ['name' => 'Climatisation', 'slug' => 'hvac'],
            ];
            foreach ($categories as $c) {
                $this->upsert('subcontractor_categories', ['slug' => $c['slug']], $c);
            }
        }
    }
}
