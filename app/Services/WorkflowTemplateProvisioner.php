<?php

namespace App\Services;

use App\Models\Workflow;

class WorkflowTemplateProvisioner
{
    public function installForOwner(int $ownerId): array
    {
        $templates = config('advanced_features.workflow.templates', []);
        $installed = [];

        foreach ($templates as $template) {
            $workflow = Workflow::query()->updateOrCreate(
                [
                    'created_by' => $ownerId,
                    'name' => $template['name'],
                ],
                [
                    'description' => $template['key'],
                    'trigger_model' => $template['trigger'],
                    'trigger_conditions' => $template['conditions'] ?? [],
                    'actions' => $template['actions'] ?? [],
                    'is_active' => true,
                ]
            );

            $installed[] = $workflow->id;
        }

        return [
            'owner_id' => $ownerId,
            'installed_count' => count($installed),
            'workflow_ids' => $installed,
        ];
    }
}
