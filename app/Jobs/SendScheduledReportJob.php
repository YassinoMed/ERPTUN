<?php

namespace App\Jobs;

use App\Models\Analytics\ReportSchedule;
use App\Models\SavedReport;
use App\Services\Core\SharedReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendScheduledReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $reportScheduleId
    ) {
    }

    public function handle(SharedReportService $reportService): void
    {
        $schedule = ReportSchedule::query()->with('user')->find($this->reportScheduleId);
        if (! $schedule || ! $schedule->is_active) {
            return;
        }

        $report = SavedReport::query()
            ->where('created_by', optional($schedule->user)->creatorId())
            ->where('report_type', $schedule->report_type)
            ->latest('id')
            ->first();

        if (! $report) {
            return;
        }

        $result = $reportService->run($report);
        foreach (($schedule->recipients ?? []) as $recipient) {
            Mail::raw(
                'Scheduled report "'.$schedule->name."\" generated with {$result['count']} row(s).",
                function ($message) use ($recipient, $schedule) {
                    $message->to($recipient)->subject('ERP report: '.$schedule->name);
                }
            );
        }
    }
}
