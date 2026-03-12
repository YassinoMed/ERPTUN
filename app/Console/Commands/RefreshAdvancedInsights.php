<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdvancedInsightEngine;
use Illuminate\Console\Command;

class RefreshAdvancedInsights extends Command
{
    protected $signature = 'erp:refresh-advanced-insights {owner_id?}';

    protected $description = 'Refresh advanced KPI snapshots, alerts and recommendations for one owner or all owners.';

    public function handle(AdvancedInsightEngine $engine): int
    {
        $ownerId = $this->argument('owner_id');

        $owners = $ownerId
            ? User::query()->where('id', $ownerId)->get()
            : User::query()->whereIn('type', ['company', 'super admin'])->get();

        if ($owners->isEmpty()) {
            $this->warn('No owners found for advanced insight refresh.');

            return self::SUCCESS;
        }

        foreach ($owners as $owner) {
            $result = $engine->refreshForOwner((int) $owner->id);
            $this->line(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return self::SUCCESS;
    }
}
