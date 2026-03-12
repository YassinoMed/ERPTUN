<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AnalyticsDashboardProvisioner;
use App\Services\WorkflowTemplateProvisioner;
use Illuminate\Console\Command;

class ProvisionAdvancedAssets extends Command
{
    protected $signature = 'erp:provision-advanced-assets {owner_id?}';

    protected $description = 'Provision advanced workflow templates and analytics dashboards for one owner or all company owners.';

    public function handle(
        WorkflowTemplateProvisioner $workflowProvisioner,
        AnalyticsDashboardProvisioner $dashboardProvisioner
    ): int {
        $ownerId = $this->argument('owner_id');

        $owners = $ownerId
            ? User::query()->where('id', $ownerId)->get()
            : User::query()->whereIn('type', ['company', 'super admin'])->get();

        if ($owners->isEmpty()) {
            $this->warn('No owners found for provisioning.');

            return self::SUCCESS;
        }

        foreach ($owners as $owner) {
            $workflowResult = $workflowProvisioner->installForOwner((int) $owner->id);
            $dashboardResult = $dashboardProvisioner->provisionForOwner((int) $owner->id);

            $this->line(json_encode([
                'owner_id' => (int) $owner->id,
                'workflows' => $workflowResult,
                'dashboard' => $dashboardResult,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return self::SUCCESS;
    }
}
