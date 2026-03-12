<?php

namespace App\Http\Controllers;

use App\Models\ApprovalFlow;
use App\Models\ApprovalRequest;
use App\Models\ApprovalStep;
use Illuminate\Http\Request;

class ApprovalFlowController extends Controller
{
    public function index()
    {
        $this->authorizeAccess('manage approval flow');

        $flows = ApprovalFlow::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->withCount('steps')
            ->latest('id')
            ->get();

        return view('approval_flow.index', compact('flows'));
    }

    public function create()
    {
        $this->authorizeAccess('create approval flow');

        return view('approval_flow.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess('create approval flow');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'resource_type' => 'nullable|string|max:255',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'default_sla_hours' => 'nullable|integer|min:1',
            'escalation_user_id' => 'nullable|integer',
            'steps' => 'nullable|array',
        ]);

        $flow = ApprovalFlow::create([
            'name' => $validated['name'],
            'resource_type' => $validated['resource_type'] ?? null,
            'min_amount' => $validated['min_amount'] ?? null,
            'max_amount' => $validated['max_amount'] ?? null,
            'default_sla_hours' => $validated['default_sla_hours'] ?? null,
            'escalation_user_id' => $validated['escalation_user_id'] ?? null,
            'allow_delegation' => $request->boolean('allow_delegation'),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => \Auth::user()->creatorId(),
        ]);

        $this->syncSteps($flow, $request->input('steps', []));

        return redirect()->route('approval-flows.index')->with('success', __('Approval flow created successfully.'));
    }

    public function show(ApprovalFlow $approvalFlow)
    {
        $this->authorizeAccess('show approval flow');
        $this->ensureOwner($approvalFlow);
        $approvalFlow->load('steps');
        $requests = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('approval_flow_id', $approvalFlow->id)
            ->latest('id')
            ->limit(25)
            ->get();

        return view('approval_flow.show', compact('approvalFlow', 'requests'));
    }

    public function edit(ApprovalFlow $approvalFlow)
    {
        $this->authorizeAccess('edit approval flow');
        $this->ensureOwner($approvalFlow);
        $approvalFlow->load('steps');

        return view('approval_flow.edit', compact('approvalFlow'));
    }

    public function update(Request $request, ApprovalFlow $approvalFlow)
    {
        $this->authorizeAccess('edit approval flow');
        $this->ensureOwner($approvalFlow);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'resource_type' => 'nullable|string|max:255',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'default_sla_hours' => 'nullable|integer|min:1',
            'escalation_user_id' => 'nullable|integer',
            'steps' => 'nullable|array',
        ]);

        $approvalFlow->update([
            'name' => $validated['name'],
            'resource_type' => $validated['resource_type'] ?? null,
            'min_amount' => $validated['min_amount'] ?? null,
            'max_amount' => $validated['max_amount'] ?? null,
            'default_sla_hours' => $validated['default_sla_hours'] ?? null,
            'escalation_user_id' => $validated['escalation_user_id'] ?? null,
            'allow_delegation' => $request->boolean('allow_delegation'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncSteps($approvalFlow, $request->input('steps', []));

        return redirect()->route('approval-flows.index')->with('success', __('Approval flow updated successfully.'));
    }

    public function destroy(ApprovalFlow $approvalFlow)
    {
        $this->authorizeAccess('delete approval flow');
        $this->ensureOwner($approvalFlow);
        $approvalFlow->delete();

        return redirect()->route('approval-flows.index')->with('success', __('Approval flow deleted successfully.'));
    }

    private function syncSteps(ApprovalFlow $flow, array $steps): void
    {
        $flow->steps()->delete();

        foreach ($steps as $index => $step) {
            if (blank($step['name'] ?? null)) {
                continue;
            }

            ApprovalStep::create([
                'approval_flow_id' => $flow->id,
                'name' => $step['name'],
                'sequence' => $index + 1,
                'approver_type' => $step['approver_type'] ?? null,
                'approver_id' => $step['approver_id'] ?? null,
                'threshold_min' => $step['threshold_min'] ?? null,
                'threshold_max' => $step['threshold_max'] ?? null,
                'sla_hours' => $step['sla_hours'] ?? null,
                'escalation_user_id' => $step['escalation_user_id'] ?? null,
                'require_reject_reason' => ! empty($step['require_reject_reason']),
                'rule' => ['note' => $step['rule_note'] ?? null],
                'created_by' => \Auth::user()->creatorId(),
            ]);
        }
    }

    private function ensureOwner(ApprovalFlow $approvalFlow): void
    {
        if ((int) $approvalFlow->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }
    }

    private function authorizeAccess(string $permission): void
    {
        if (! \Auth::user()->can($permission)) {
            abort(403, 'Permission denied.');
        }
    }
}
