<?php

namespace App\Jobs;

use App\Models\ExportJob;
use App\Services\Core\DataExchangeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $exportJobId
    ) {
    }

    public function handle(DataExchangeService $dataExchange): void
    {
        $job = ExportJob::query()->find($this->exportJobId);

        if (! $job) {
            return;
        }

        $dataExchange->runExport($job);
    }
}
