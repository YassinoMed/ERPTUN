<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\WorkflowExecution;

class WorkflowEngine
{
    public function __construct(
        private readonly WorkflowConditionEvaluator $conditionEvaluator,
        private readonly WorkflowActionExecutor $actionExecutor
    ) {
    }

    public function execute(Workflow $workflow, $model, array $context = []): WorkflowExecution
    {
        $context = array_merge([
            'workflow_name' => $workflow->name,
            'triggered_by' => auth()->id(),
            'owner_id' => $model->created_by ?? null,
        ], $context);

        if (! $this->conditionEvaluator->passes($model, $workflow->trigger_conditions ?? [], $context)) {
            return WorkflowExecution::create([
                'workflow_id' => $workflow->id,
                'triggered_by' => $context['triggered_by'],
                'model_id' => $model->id ?? null,
                'model_type' => get_class($model),
                'execution_data' => [
                    'context' => $context,
                    'actions' => $workflow->actions,
                    'results' => [],
                    'reason' => 'Conditions not met',
                ],
                'status' => 'skipped',
            ]);
        }

        $results = [];
        foreach ($workflow->actions ?? [] as $action) {
            try {
                $results[] = $this->actionExecutor->execute($action, $model, $context);
            } catch (\Throwable $throwable) {
                $results[] = [
                    'type' => $action['type'] ?? 'unknown',
                    'status' => 'failed',
                    'message' => $throwable->getMessage(),
                ];
            }
        }

        $status = collect($results)->contains(fn (array $result) => $result['status'] === 'failed')
            ? 'partial_failed'
            : 'success';

        if (empty($results)) {
            $status = 'skipped';
        }

        return WorkflowExecution::create([
            'workflow_id' => $workflow->id,
            'triggered_by' => $context['triggered_by'],
            'model_id' => $model->id ?? null,
            'model_type' => get_class($model),
            'execution_data' => [
                'context' => $context,
                'actions' => $workflow->actions,
                'results' => $results,
            ],
            'status' => $status,
        ]);
    }
}
