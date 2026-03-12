<?php

namespace App\Http\Controllers;

use App\Models\ExportJob;
use App\Models\ImportJob;
use App\Models\SavedReport;
use App\Models\Analytics\ReportSchedule;
use App\Jobs\SendScheduledReportJob;
use App\Services\Core\DataExchangeService;
use App\Services\Core\SharedReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class CoreDataExchangeController extends Controller
{
    public function __construct(
        private readonly DataExchangeService $dataExchange,
        private readonly SharedReportService $reportService
    ) {
    }

    public function imports()
    {
        $this->authorizeAccess('manage import job');

        $imports = ImportJob::query()->where('created_by', \Auth::user()->creatorId())->latest('id')->paginate(20);

        return view('core_data.imports', compact('imports'));
    }

    public function importShow(ImportJob $importJob)
    {
        $this->ensureOwner($importJob, 'show import job');

        return view('core_data.import_show', compact('importJob'));
    }

    public function importMapping(Request $request, ImportJob $importJob)
    {
        $this->ensureOwner($importJob, 'edit import job');
        $importJob->mapping = $request->input('mapping', []);
        $importJob->save();

        return redirect()->back()->with('success', __('Import mapping saved.'));
    }

    public function importRollback(ImportJob $importJob)
    {
        $this->ensureOwner($importJob, 'edit import job');
        $rolledBack = $this->dataExchange->rollbackImport($importJob);

        return redirect()->back()->with('success', __('Import rollback executed.'));
    }

    public function exports()
    {
        $this->authorizeAccess('manage export job');

        $exports = ExportJob::query()->where('created_by', \Auth::user()->creatorId())->latest('id')->paginate(20);
        $exportStats = [
            'queued' => ExportJob::query()->where('created_by', \Auth::user()->creatorId())->where('status', 'queued')->count(),
            'processing' => ExportJob::query()->where('created_by', \Auth::user()->creatorId())->where('status', 'processing')->count(),
            'completed' => ExportJob::query()->where('created_by', \Auth::user()->creatorId())->where('status', 'completed')->count(),
            'failed' => ExportJob::query()->where('created_by', \Auth::user()->creatorId())->where('status', 'failed')->count(),
        ];

        return view('core_data.exports', compact('exports', 'exportStats'));
    }

    public function exportShow(ExportJob $exportJob)
    {
        $this->ensureOwner($exportJob, 'manage export job');

        return view('core_data.export_show', compact('exportJob'));
    }

    public function exportCreate()
    {
        $this->authorizeAccess('create export job');

        return view('core_data.export_create');
    }

    public function exportStore(Request $request)
    {
        $this->authorizeAccess('create export job');

        $validated = $request->validate([
            'module' => 'required|string|max:255',
            'format' => 'required|string|max:20',
            'scheduled_for' => 'nullable|date',
        ]);

        $this->dataExchange->scheduleExport([
            'created_by' => \Auth::user()->creatorId(),
            'user_id' => \Auth::id(),
            'module' => $validated['module'],
            'format' => $validated['format'],
            'filters' => $request->input('filters', []),
            'status' => 'queued',
            'scheduled_for' => $validated['scheduled_for'] ?? now(),
        ]);

        return redirect()->route('core.exports.index')->with('success', __('Export scheduled successfully.'));
    }

    public function exportRun(ExportJob $exportJob)
    {
        $this->ensureOwner($exportJob, 'create export job');

        try {
            $this->dataExchange->runExport($exportJob);
        } catch (\Throwable $exception) {
            return redirect()->back()->with('error', __('Export failed: ').$exception->getMessage());
        }

        return redirect()->route('core.exports.show', $exportJob)->with('success', __('Export executed successfully.'));
    }

    public function exportDispatchDue()
    {
        $this->authorizeAccess('manage export job');
        $count = $this->dataExchange->dispatchDueExports();

        return redirect()->route('core.exports.index')->with('success', __('Queued :count export job(s) for background processing.', ['count' => $count]));
    }

    public function exportDownload(ExportJob $exportJob)
    {
        $this->ensureOwner($exportJob, 'manage export job');

        if (! $exportJob->file_path || ! Storage::disk('local')->exists($exportJob->file_path)) {
            return redirect()->back()->with('error', __('Export file not found.'));
        }

        return Storage::disk('local')->download($exportJob->file_path);
    }

    public function reports()
    {
        $this->authorizeAccess('manage saved report');

        $reports = SavedReport::query()->where('created_by', \Auth::user()->creatorId())->latest('id')->get();
        $reportStats = [
            'total' => $reports->count(),
            'shared' => $reports->where('is_shared', true)->count(),
            'scheduled' => ReportSchedule::query()->where('user_id', \Auth::id())->count(),
            'activeSchedules' => ReportSchedule::query()->where('user_id', \Auth::id())->where('is_active', true)->count(),
        ];

        return view('core_data.reports', compact('reports', 'reportStats'));
    }

    public function reportCreate()
    {
        $this->authorizeAccess('create saved report');

        return view('core_data.report_create');
    }

    public function reportStore(Request $request)
    {
        $this->authorizeAccess('create saved report');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'report_type' => 'required|string|max:255',
        ]);

        SavedReport::create([
            'created_by' => \Auth::user()->creatorId(),
            'user_id' => \Auth::id(),
            'name' => $validated['name'],
            'report_type' => $validated['report_type'],
            'filters' => $request->input('filters', []),
            'columns' => array_values(array_filter((array) $request->input('columns', []))),
            'is_shared' => $request->boolean('is_shared'),
        ]);

        return redirect()->route('core.reports.index')->with('success', __('Saved report created successfully.'));
    }

    public function reportShow(SavedReport $savedReport)
    {
        $this->ensureOwner($savedReport, 'show saved report');
        $result = $this->reportService->run($savedReport);
        $schedules = ReportSchedule::query()->where('user_id', \Auth::id())->where('report_type', $savedReport->report_type)->get();

        return view('core_data.report_show', compact('savedReport', 'result', 'schedules'));
    }

    public function reportSchedule(Request $request, SavedReport $savedReport)
    {
        $this->ensureOwner($savedReport, 'edit saved report');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|string|max:50',
            'recipients' => 'required|string',
        ]);

        ReportSchedule::create([
            'user_id' => \Auth::id(),
            'name' => $validated['name'],
            'report_type' => $savedReport->report_type,
            'frequency' => $validated['frequency'],
            'recipients' => array_values(array_filter(array_map('trim', explode(',', $validated['recipients'])))),
            'filters' => $savedReport->filters,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', __('Report schedule created.'));
    }

    public function reportScheduleToggle(ReportSchedule $reportSchedule)
    {
        if (! \Auth::user()->can('edit saved report') || (int) $reportSchedule->user_id !== (int) \Auth::id()) {
            abort(403, 'Permission denied.');
        }

        $reportSchedule->is_active = ! $reportSchedule->is_active;
        $reportSchedule->save();

        return redirect()->back()->with('success', __('Report schedule updated.'));
    }

    public function reportScheduleSend(ReportSchedule $reportSchedule)
    {
        if (! \Auth::user()->can('show saved report') || (int) $reportSchedule->user_id !== (int) \Auth::id()) {
            abort(403, 'Permission denied.');
        }

        $report = SavedReport::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('report_type', $reportSchedule->report_type)
            ->latest('id')
            ->first();

        if (! $report) {
            return redirect()->back()->with('error', __('No saved report matches this schedule.'));
        }

        $result = $this->reportService->run($report);
        foreach (($reportSchedule->recipients ?? []) as $recipient) {
            Mail::raw(
                'Scheduled report "'.$reportSchedule->name."\" generated with {$result['count']} row(s).",
                function ($message) use ($recipient, $reportSchedule) {
                    $message->to($recipient)->subject('ERP report: '.$reportSchedule->name);
                }
            );
        }

        $reportSchedule->last_sent_at = now();
        $reportSchedule->save();

        return redirect()->back()->with('success', __('Report schedule sent successfully.'));
    }

    public function reportScheduleDispatchDue()
    {
        $this->authorizeAccess('manage saved report');

        $count = 0;
        foreach (ReportSchedule::query()->where('is_active', true)->where('user_id', \Auth::id())->get() as $schedule) {
            if (! $schedule->shouldSend()) {
                continue;
            }

            $schedule->last_sent_at = now();
            $schedule->save();
            SendScheduledReportJob::dispatch($schedule->id);
            $count++;
        }

        return redirect()->route('core.reports.index')->with('success', __('Queued :count scheduled report delivery job(s).', ['count' => $count]));
    }

    private function authorizeAccess(string $permission): void
    {
        if (! \Auth::user()->can($permission)) {
            abort(403, 'Permission denied.');
        }
    }

    private function ensureOwner($model, string $permission): void
    {
        if (! \Auth::user()->can($permission) || (int) $model->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }
    }
}
