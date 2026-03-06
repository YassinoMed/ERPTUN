<?php

namespace Database\Seeders\Modules;

class CooperativeSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Cooperative';

    protected function seed(): void
    {
        if ($this->tableExists('cooperative_types')) {
            $types = [
                ['name' => 'Coopérative agricole', 'slug' => 'agricultural', 'description' => 'Production et commercialisation agricole'],
                ['name' => 'Coopérative laitière', 'slug' => 'dairy', 'description' => 'Collecte et transformation du lait'],
                ['name' => 'Coopérative oléicole', 'slug' => 'olive_oil', 'description' => 'Production d\'huile d\'olive'],
                ['name' => 'Coopérative céréalière', 'slug' => 'cereal', 'description' => 'Stockage et vente de céréales'],
                ['name' => 'Coopérative artisanale', 'slug' => 'artisan', 'description' => 'Production artisanale'],
                ['name' => 'Coopérative de pêche', 'slug' => 'fishing', 'description' => 'Pêche et aquaculture'],
            ];
            foreach ($types as $t) {
                $this->upsert('cooperative_types', ['slug' => $t['slug']], $t);
            }
        }

        if ($this->tableExists('member_roles')) {
            $roles = [
                ['name' => 'Président', 'slug' => 'president', 'level' => 1],
                ['name' => 'Vice-Président', 'slug' => 'vice_president', 'level' => 2],
                ['name' => 'Trésorier', 'slug' => 'treasurer', 'level' => 3],
                ['name' => 'Secrétaire', 'slug' => 'secretary', 'level' => 4],
                ['name' => 'Membre actif', 'slug' => 'active_member', 'level' => 5],
                ['name' => 'Membre associé', 'slug' => 'associate_member', 'level' => 6],
            ];
            foreach ($roles as $r) {
                $this->upsert('member_roles', ['slug' => $r['slug']], $r);
            }
        }

        if ($this->tableExists('cooperative_settings')) {
            $settings = [
                ['key' => 'fiscal_year_start', 'value' => '01-01'],
                ['key' => 'min_members', 'value' => '7'],
                ['key' => 'share_value', 'value' => '100'],
                ['key' => 'max_dividend_percent', 'value' => '6'],
                ['key' => 'annual_assembly_month', 'value' => '3'],
            ];
            foreach ($settings as $s) {
                $this->upsert('cooperative_settings', ['key' => $s['key']], $s);
            }
        }
    }
}
