<?php

namespace App\Services;

class WorkflowConditionEvaluator
{
    public function passes($model, array $conditions = [], array $context = []): bool
    {
        foreach ($conditions as $condition) {
            if (! $this->passesCondition($model, $condition, $context)) {
                return false;
            }
        }

        return true;
    }

    public function passesCondition($model, array $condition, array $context = []): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $expected = $condition['value'] ?? null;

        if (! $field) {
            return true;
        }

        $actual = $this->resolveValue($model, $field, $context);

        return match ($operator) {
            'equals', 'eq' => (string) $actual === (string) $expected,
            'not_equals', 'neq' => (string) $actual !== (string) $expected,
            'contains' => str_contains((string) $actual, (string) $expected),
            'greater_than', 'gt' => (float) $actual > (float) $expected,
            'greater_than_or_equal', 'gte' => (float) $actual >= (float) $expected,
            'less_than', 'lt' => (float) $actual < (float) $expected,
            'less_than_or_equal', 'lte' => (float) $actual <= (float) $expected,
            'in' => in_array($actual, (array) $expected, true),
            'not_in' => ! in_array($actual, (array) $expected, true),
            'is_empty' => blank($actual),
            'is_not_empty' => filled($actual),
            'is_true' => filter_var($actual, FILTER_VALIDATE_BOOLEAN),
            'is_false' => ! filter_var($actual, FILTER_VALIDATE_BOOLEAN),
            default => false,
        };
    }

    public function resolveValue($model, string $path, array $context = [])
    {
        if (str_starts_with($path, 'context.')) {
            return data_get($context, substr($path, 8));
        }

        return data_get($model, $path);
    }
}
