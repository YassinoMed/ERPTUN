<?php

namespace App\Services\Core;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\SavedReport;

class SharedReportService
{
    public function run(SavedReport $report): array
    {
        $builder = match ($report->report_type) {
            'customers' => Customer::query()->where('created_by', $report->created_by),
            'purchases' => Purchase::query()->where('created_by', $report->created_by),
            default => Invoice::query()->where('created_by', $report->created_by),
        };

        foreach (($report->filters ?? []) as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $builder->where($field, $value);
        }

        $columns = ! empty($report->columns) ? $report->columns : ['id'];
        $rows = $builder->limit(200)->get($columns);

        $report->last_run_at = now();
        $report->save();

        return [
            'columns' => $columns,
            'rows' => $rows->toArray(),
            'count' => $rows->count(),
        ];
    }
}
