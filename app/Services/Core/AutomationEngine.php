<?php

namespace App\Services\Core;

use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Services\WorkflowActionExecutor;

class AutomationEngine
{
    public function __construct(
        private readonly WorkflowActionExecutor $actionExecutor
    ) {
    }

    public function handle(string $event, $model, int $createdBy, array $context = []): void
    {
        $rules = AutomationRule::query()
            ->where('created_by', $createdBy)
            ->where('event_name', $event)
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        foreach ($rules as $rule) {
            $this->runRule($rule, $event, $model, $createdBy, $context);
        }
    }

    public function runRule(AutomationRule $rule, string $event, $model, int $createdBy, array $context = []): AutomationLog
    {
        $log = AutomationLog::create([
            'automation_rule_id' => $rule->id,
            'created_by' => $createdBy,
            'event_name' => $event,
            'model_type' => is_object($model) ? get_class($model) : null,
            'model_id' => $model->id ?? null,
            'payload' => [
                'context' => $context,
                'attributes' => is_object($model) && method_exists($model, 'getAttributes') ? $model->getAttributes() : $model,
            ],
            'status' => 'pending',
            'triggered_at' => now(),
        ]);

        if (! $this->passesConditions($rule->conditions ?? [], $model, $context)) {
            $log->status = 'skipped';
            $log->result = ['reason' => 'Conditions not met'];
            $log->save();

            return $log;
        }

        $results = [];
        foreach ($rule->actions ?? [] as $action) {
            try {
                $results[] = $this->actionExecutor->execute($action, $model, array_merge($context, [
                    'event' => $event,
                    'owner_id' => $createdBy,
                ]));
            } catch (\Throwable $throwable) {
                $results[] = [
                    'type' => $action['type'] ?? 'unknown',
                    'status' => 'failed',
                    'message' => $throwable->getMessage(),
                ];
            }
        }

        $rule->last_triggered_at = now();
        $rule->save();

        $log->status = collect($results)->contains(fn (array $result) => ($result['status'] ?? null) === 'failed') ? 'partial_failed' : 'success';
        $log->result = $results;
        $log->save();

        return $log;
    }

    private function passesConditions(array $conditions, $model, array $context = []): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $expected = $condition['value'] ?? null;
            $actual = str_starts_with((string) $field, 'context.')
                ? data_get($context, substr((string) $field, 8))
                : data_get($model, (string) $field);

            $passed = match ($operator) {
                'not_equals' => $actual != $expected,
                'contains' => str_contains((string) $actual, (string) $expected),
                'greater_than' => (float) $actual > (float) $expected,
                'less_than' => (float) $actual < (float) $expected,
                'in' => in_array($actual, (array) $expected, true),
                default => $actual == $expected,
            };

            if (! $passed) {
                return false;
            }
        }

        return true;
    }
}
