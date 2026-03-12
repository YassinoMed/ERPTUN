<?php

namespace App\Http\Controllers;

use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Services\Core\AutomationEngine;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    public function __construct(
        private readonly AutomationEngine $automationEngine
    ) {
    }

    public function index()
    {
        $this->authorizeAccess('manage automation rule');

        $rules = AutomationRule::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();
        $recentFailedLogs = AutomationLog::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->whereIn('status', ['failed', 'partial_failed'])
            ->with('automationRule')
            ->latest('id')
            ->limit(10)
            ->get();
        $stats = [
            'rules' => $rules->count(),
            'active' => $rules->where('is_active', true)->count(),
            'triggered' => $rules->whereNotNull('last_triggered_at')->count(),
            'failedLogs' => $recentFailedLogs->count(),
        ];

        return view('automation_rule.index', compact('rules', 'stats', 'recentFailedLogs'));
    }

    public function create()
    {
        $this->authorizeAccess('create automation rule');

        return view('automation_rule.create', $this->catalogs());
    }

    public function store(Request $request)
    {
        $this->authorizeAccess('create automation rule');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'priority' => 'nullable|integer|min:0',
            'conditions' => 'nullable|array',
            'actions' => 'nullable|array',
        ]);

        AutomationRule::create([
            'created_by' => \Auth::user()->creatorId(),
            'name' => $validated['name'],
            'event_name' => $validated['event_name'],
            'description' => $request->description,
            'priority' => $validated['priority'] ?? 0,
            'conditions' => $this->normalizeRows($validated['conditions'] ?? []),
            'actions' => $this->normalizeActions($validated['actions'] ?? []),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('automation-rules.index')->with('success', __('Automation rule created successfully.'));
    }

    public function show(AutomationRule $automationRule)
    {
        $this->authorizeAccess('show automation rule');
        $this->ensureOwner($automationRule);
        $logs = AutomationLog::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('automation_rule_id', $automationRule->id)
            ->latest('id')
            ->limit(50)
            ->get();
        $simulationCandidates = $logs
            ->filter(fn ($log) => filled($log->model_type) && filled($log->model_id))
            ->take(10)
            ->values();

        return view('automation_rule.show', array_merge(
            compact('automationRule', 'logs', 'simulationCandidates'),
            $this->catalogs()
        ));
    }

    public function edit(AutomationRule $automationRule)
    {
        $this->authorizeAccess('edit automation rule');
        $this->ensureOwner($automationRule);

        return view('automation_rule.edit', array_merge(compact('automationRule'), $this->catalogs()));
    }

    public function update(Request $request, AutomationRule $automationRule)
    {
        $this->authorizeAccess('edit automation rule');
        $this->ensureOwner($automationRule);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'priority' => 'nullable|integer|min:0',
            'conditions' => 'nullable|array',
            'actions' => 'nullable|array',
        ]);

        $automationRule->update([
            'name' => $validated['name'],
            'event_name' => $validated['event_name'],
            'description' => $request->description,
            'priority' => $validated['priority'] ?? 0,
            'conditions' => $this->normalizeRows($validated['conditions'] ?? []),
            'actions' => $this->normalizeActions($validated['actions'] ?? []),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('automation-rules.index')->with('success', __('Automation rule updated successfully.'));
    }

    public function destroy(AutomationRule $automationRule)
    {
        $this->authorizeAccess('delete automation rule');
        $this->ensureOwner($automationRule);
        $automationRule->delete();

        return redirect()->route('automation-rules.index')->with('success', __('Automation rule deleted successfully.'));
    }

    public function retry(AutomationLog $automationLog)
    {
        if (! \Auth::user()->can('edit automation rule') || (int) $automationLog->created_by !== (int) \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if (! $automationLog->model_type || ! $automationLog->model_id || ! class_exists($automationLog->model_type)) {
            return redirect()->back()->with('error', __('Automation log cannot be retried.'));
        }

        $model = $automationLog->model_type::find($automationLog->model_id);
        if (! $model) {
            return redirect()->back()->with('error', __('Related record not found.'));
        }

        $this->automationEngine->handle($automationLog->event_name, $model, \Auth::user()->creatorId(), [
            'triggered_by' => \Auth::id(),
            'retry_log_id' => $automationLog->id,
        ]);

        return redirect()->back()->with('success', __('Automation retried successfully.'));
    }

    public function simulate(Request $request, AutomationRule $automationRule)
    {
        $this->authorizeAccess('edit automation rule');
        $this->ensureOwner($automationRule);

        $validated = $request->validate([
            'automation_log_id' => 'required|integer|exists:automation_logs,id',
        ]);

        $automationLog = AutomationLog::query()
            ->where('id', $validated['automation_log_id'])
            ->where('created_by', \Auth::user()->creatorId())
            ->where('automation_rule_id', $automationRule->id)
            ->firstOrFail();

        if (! $automationLog->model_type || ! $automationLog->model_id || ! class_exists($automationLog->model_type)) {
            return redirect()->back()->with('error', __('Selected automation log cannot be simulated.'));
        }

        $model = $automationLog->model_type::find($automationLog->model_id);
        if (! $model) {
            return redirect()->back()->with('error', __('Simulation target no longer exists.'));
        }

        $simulationLog = $this->automationEngine->runRule(
            $automationRule,
            $automationLog->event_name,
            $model,
            \Auth::user()->creatorId(),
            array_merge($automationLog->payload['context'] ?? [], [
                'triggered_by' => \Auth::id(),
                'simulation' => true,
                'source_log_id' => $automationLog->id,
            ])
        );

        return redirect()->route('automation-rules.show', $automationRule)->with(
            'success',
            __('Automation simulation completed with status: :status', ['status' => $simulationLog->status])
        );
    }

    public function toggleStatus(AutomationRule $automationRule)
    {
        $this->authorizeAccess('edit automation rule');
        $this->ensureOwner($automationRule);

        $automationRule->is_active = ! $automationRule->is_active;
        $automationRule->save();

        return redirect()->route('automation-rules.index')->with(
            'success',
            $automationRule->is_active ? __('Automation rule activated successfully.') : __('Automation rule paused successfully.')
        );
    }

    public function duplicate(AutomationRule $automationRule)
    {
        $this->authorizeAccess('create automation rule');
        $this->ensureOwner($automationRule);

        $duplicate = $automationRule->replicate(['last_triggered_at']);
        $duplicate->name = $automationRule->name.' '.__('(Copy)');
        $duplicate->is_active = false;
        $duplicate->last_triggered_at = null;
        $duplicate->save();

        return redirect()->route('automation-rules.edit', $duplicate)->with(
            'success',
            __('Automation rule duplicated. Review and activate when ready.')
        );
    }

    private function normalizeRows(array $rows): array
    {
        return collect($rows)
            ->filter(fn ($row) => filled($row['field'] ?? null))
            ->map(fn ($row) => [
                'field' => $row['field'] ?? null,
                'operator' => $row['operator'] ?? 'equals',
                'value' => $row['value'] ?? null,
            ])->values()->all();
    }

    private function normalizeActions(array $rows): array
    {
        return collect($rows)
            ->filter(fn ($row) => filled($row['type'] ?? null))
            ->map(function ($row) {
                $data = [];
                foreach (($row['data'] ?? []) as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $data[$key] = $value;
                    }
                }

                return [
                    'type' => $row['type'],
                    'data' => $data,
                ];
            })->values()->all();
    }

    private function ensureOwner(AutomationRule $automationRule): void
    {
        if ((int) $automationRule->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }
    }

    private function authorizeAccess(string $permission): void
    {
        if (! \Auth::user()->can($permission)) {
            abort(403, 'Permission denied.');
        }
    }

    private function catalogs(): array
    {
        return [
            'eventCatalog' => [
                'invoice.created',
                'invoice.updated',
                'purchase.created',
                'support.created',
                'medical_appointment.created',
                'workflow.approval_requested',
                'workflow.approved',
            ],
            'conditionFields' => [
                'status',
                'amount',
                'priority',
                'created_by',
                'context.owner_id',
                'context.triggered_by',
            ],
            'actionCatalog' => [
                'notification' => __('In-app notification'),
                'email' => __('Email'),
                'task' => __('Task'),
                'update_field' => __('Update field'),
                'webhook' => __('Webhook'),
                'audit_log' => __('Audit log'),
                'approval_request' => __('Approval request'),
                'zapier_hook' => __('Zapier hook'),
            ],
        ];
    }
}
