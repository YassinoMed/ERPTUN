<?php

namespace App\Services\Core;

use App\Models\Customer;
use App\Models\ExportJob;
use App\Models\ImportJob;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\ProductService;
use App\Models\Purchase;
use App\Models\Vender;
use App\Jobs\RunExportJob;
use Illuminate\Support\Facades\Storage;

class DataExchangeService
{
    public function previewCsv(string $path, array $mapping = [], int $limit = 5): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = fgetcsv($handle) ?: [];
        $rows = [];

        while (($row = fgetcsv($handle)) !== false && count($rows) < $limit) {
            $rows[] = array_combine($headers, array_pad($row, count($headers), null));
        }

        fclose($handle);

        return [
            'headers' => $headers,
            'mapping' => $mapping,
            'rows' => $rows,
        ];
    }

    public function startImport(array $data): ImportJob
    {
        return ImportJob::create($data);
    }

    public function completeImport(ImportJob $job, array $summary = [], ?array $rollbackPayload = null): ImportJob
    {
        $job->status = 'completed';
        $job->summary = $summary;
        $job->rollback_payload = $rollbackPayload;
        $job->processed_at = now();
        $job->save();

        return $job;
    }

    public function scheduleExport(array $data): ExportJob
    {
        return ExportJob::create($data);
    }

    public function runExport(ExportJob $job): ExportJob
    {
        $job->status = 'processing';
        $job->started_at = now();
        $job->attempts = (int) $job->attempts + 1;
        $job->error_message = null;
        $job->save();

        try {
            $rows = $this->resolveRows($job);
            $extension = strtolower($job->format ?: 'json');
            $path = 'exports/'.$job->module.'-'.$job->id.'-'.now()->format('YmdHis').'.'.$extension;

            if ($extension === 'csv') {
                $content = $this->toCsv($rows);
            } else {
                $content = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }

            Storage::disk('local')->put($path, (string) $content);

            $job->status = 'completed';
            $job->file_path = $path;
            $job->completed_at = now();
            $job->save();
        } catch (\Throwable $exception) {
            $job->status = 'failed';
            $job->error_message = mb_substr($exception->getMessage(), 0, 65535);
            $job->save();

            throw $exception;
        }

        return $job->refresh();
    }

    public function runDueExports(): int
    {
        $count = 0;
        $jobs = ExportJob::query()
            ->where('status', 'queued')
            ->where(function ($query) {
                $query->whereNull('scheduled_for')->orWhere('scheduled_for', '<=', now());
            })
            ->get();

        foreach ($jobs as $job) {
            $this->runExport($job);
            $count++;
        }

        return $count;
    }

    public function dispatchDueExports(): int
    {
        $count = 0;
        $jobs = ExportJob::query()
            ->where('status', 'queued')
            ->where(function ($query) {
                $query->whereNull('scheduled_for')->orWhere('scheduled_for', '<=', now());
            })
            ->get();

        foreach ($jobs as $job) {
            $job->status = 'processing';
            $job->started_at = now();
            $job->save();
            RunExportJob::dispatch($job->id);
            $count++;
        }

        return $count;
    }

    public function rollbackImport(ImportJob $importJob): int
    {
        $payload = $importJob->rollback_payload ?? [];
        $ids = (array) ($payload['created_ids'] ?? []);
        if ($ids === []) {
            return 0;
        }

        $rolledBack = match ($importJob->module) {
            'customers' => Customer::query()->whereIn('id', $ids)->delete(),
            'venders' => Vender::query()->whereIn('id', $ids)->delete(),
            'patients' => Patient::query()->whereIn('id', $ids)->delete(),
            'product_services' => ProductService::query()->whereIn('id', $ids)->delete(),
            default => 0,
        };

        $importJob->status = 'rolled_back';
        $importJob->summary = array_merge($importJob->summary ?? [], ['rolled_back' => $rolledBack]);
        $importJob->save();

        return $rolledBack;
    }

    private function resolveRows(ExportJob $job): array
    {
        $builder = match ($job->module) {
            'customers' => Customer::query()->where('created_by', $job->created_by),
            'venders' => Vender::query()->where('created_by', $job->created_by),
            'patients' => Patient::query()->where('created_by', $job->created_by),
            'product_services' => ProductService::query()->where('created_by', $job->created_by),
            'purchases' => Purchase::query()->where('created_by', $job->created_by)->with(['vender', 'category']),
            default => Invoice::query()->where('created_by', $job->created_by)->with('customer'),
        };

        foreach (($job->filters ?? []) as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $builder->where($field, $value);
        }

        return $builder->limit(1000)->get()->toArray();
    }

    private function toCsv(array $rows): string
    {
        if (empty($rows)) {
            return '';
        }

        $flattenedRows = array_map(function ($row) {
            $result = [];
            foreach ($row as $key => $value) {
                $result[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES) : $value;
            }

            return $result;
        }, $rows);

        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, array_keys($flattenedRows[0]));
        foreach ($flattenedRows as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return $csv;
    }
}
