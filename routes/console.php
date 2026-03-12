<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendScheduledReportJob;
use App\Models\Analytics\ReportSchedule;
use App\Services\Core\WorkflowApprovalService;
use App\Services\Core\DataQualityService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('education:seed', function () {
    $this->call('db:seed', ['--class' => 'Database\\Seeders\\EducationSeeder']);
})->purpose('Seed education sample data');

Artisan::command('core:escalate-approvals', function () {
    $count = app(WorkflowApprovalService::class)->escalateOverdue();
    $this->info('Escalated approvals: '.$count);
})->purpose('Escalate overdue approval requests');

Artisan::command('core:scan-data-quality', function () {
    $service = app(DataQualityService::class);
    $tenants = \App\Models\User::query()->where('type', 'company')->pluck('id');
    $count = 0;
    foreach ($tenants as $tenantId) {
        $count += $service->scanCustomerDuplicates($tenantId);
        $count += $service->scanVenderDuplicates($tenantId);
    }
    $this->info('Data quality issues detected: '.$count);
})->purpose('Scan duplicate core records');

Artisan::command('core:run-exports', function () {
    $count = app(\App\Services\Core\DataExchangeService::class)->dispatchDueExports();
    $this->info('Queued export jobs: '.$count);
})->purpose('Run scheduled export jobs');

Artisan::command('core:run-report-schedules', function () {
    $sent = 0;

    foreach (ReportSchedule::query()->where('is_active', true)->get() as $schedule) {
        if (! $schedule->shouldSend()) {
            continue;
        }

        $schedule->last_sent_at = now();
        $schedule->save();
        SendScheduledReportJob::dispatch($schedule->id);
        $sent++;
    }

    $this->info('Scheduled report jobs queued: '.$sent);
})->purpose('Send scheduled report digests');
