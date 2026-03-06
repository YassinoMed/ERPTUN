<?php

namespace Database\Seeders\Modules;

class HedgingSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Hedging';

    protected function seed(): void
    {
        if ($this->tableExists('commodity_types')) {
            $commodities = [
                ['name' => 'Blé tendre', 'slug' => 'soft_wheat', 'unit' => 'tonne', 'exchange' => 'MATIF'],
                ['name' => 'Maïs', 'slug' => 'corn', 'unit' => 'tonne', 'exchange' => 'CBOT'],
                ['name' => 'Sucre', 'slug' => 'sugar', 'unit' => 'tonne', 'exchange' => 'ICE'],
                ['name' => 'Café Arabica', 'slug' => 'arabica_coffee', 'unit' => 'lb', 'exchange' => 'ICE'],
                ['name' => 'Huile d\'olive', 'slug' => 'olive_oil', 'unit' => 'tonne', 'exchange' => 'OTC'],
                ['name' => 'Colza', 'slug' => 'rapeseed', 'unit' => 'tonne', 'exchange' => 'MATIF'],
                ['name' => 'Soja', 'slug' => 'soybean', 'unit' => 'bushel', 'exchange' => 'CBOT'],
            ];
            foreach ($commodities as $c) {
                $this->upsert('commodity_types', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('contract_types')) {
            $contracts = [
                ['name' => 'Forward', 'slug' => 'forward', 'description' => 'Contrat à terme de gré à gré'],
                ['name' => 'Futures', 'slug' => 'futures', 'description' => 'Contrat à terme standardisé'],
                ['name' => 'Option Call', 'slug' => 'call_option', 'description' => 'Option d\'achat'],
                ['name' => 'Option Put', 'slug' => 'put_option', 'description' => 'Option de vente'],
                ['name' => 'Swap', 'slug' => 'swap', 'description' => 'Contrat d\'échange de flux'],
            ];
            foreach ($contracts as $c) {
                $this->upsert('contract_types', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('risk_levels')) {
            $risks = [
                ['name' => 'Très faible', 'slug' => 'very_low', 'level' => 1, 'color' => '#27AE60'],
                ['name' => 'Faible', 'slug' => 'low', 'level' => 2, 'color' => '#2ECC71'],
                ['name' => 'Modéré', 'slug' => 'moderate', 'level' => 3, 'color' => '#F39C12'],
                ['name' => 'Élevé', 'slug' => 'high', 'level' => 4, 'color' => '#E67E22'],
                ['name' => 'Critique', 'slug' => 'critical', 'level' => 5, 'color' => '#E74C3C'],
            ];
            foreach ($risks as $r) {
                $this->upsert('risk_levels', ['slug' => $r['slug']], $r);
            }
        }
    }
}
