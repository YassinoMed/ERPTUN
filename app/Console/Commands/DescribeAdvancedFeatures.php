<?php

namespace App\Console\Commands;

use App\Services\AdvancedFeatureCatalog;
use Illuminate\Console\Command;

class DescribeAdvancedFeatures extends Command
{
    protected $signature = 'erp:advanced-features {module?}';

    protected $description = 'Display the advanced ERP feature catalog or a single module definition.';

    public function handle(AdvancedFeatureCatalog $catalog): int
    {
        $module = $this->argument('module');

        if ($module) {
            $definition = $catalog->module($module);

            if (! $definition) {
                $this->error(sprintf('Unknown module: %s', $module));

                return self::FAILURE;
            }

            $this->line(json_encode($definition, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $payload = [
            'modules' => $catalog->prioritizedModules(),
            'roadmap' => $catalog->roadmap(),
            'workflow' => $catalog->workflowCatalog(),
        ];

        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
