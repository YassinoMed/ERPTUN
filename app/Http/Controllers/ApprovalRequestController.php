<?php

namespace App\Http\Controllers;

use App\Models\ApprovalFlow;
use App\Models\ApprovalRequest;
use App\Models\DeliveryNote;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\MedicalInvoice;
use App\Models\Purchase;
use App\Models\User;
use App\Services\Core\WorkflowApprovalService;
use Illuminate\Http\Request;

class ApprovalRequestController extends Controller
{
    public function __construct(
        private readonly WorkflowApprovalService $approvalService
    ) {
    }

    public function index(Request $request)
    {
        if (! \Auth::user()->can('manage approval request') && ! \Auth::user()->can('show approval request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $status = $request->query('status');
        $assigned = $request->boolean('assigned');
        $query = trim((string) $request->query('q'));
        $flowId = $request->query('flow');
        $requests = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->with(['approvalFlow', 'currentStep', 'actions', 'requester', 'delegatedUser'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($assigned, fn ($q) => $q->where('delegated_to', \Auth::id()))
            ->when($flowId, fn ($q) => $q->where('approval_flow_id', $flowId))
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($nested) use ($query) {
                    $nested->where('resource_id', 'like', '%'.$query.'%')
                        ->orWhereHas('approvalFlow', fn ($flow) => $flow->where('name', 'like', '%'.$query.'%'))
                        ->orWhereHas('requester', fn ($requester) => $requester->where('name', 'like', '%'.$query.'%'))
                        ->orWhereHas('delegatedUser', fn ($delegate) => $delegate->where('name', 'like', '%'.$query.'%'));
                });
            })
            ->latest('id')
            ->paginate(20);

        $overdueCount = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now())
            ->count();

        $pendingCount = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('status', 'pending')
            ->count();

        $assignedCount = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('delegated_to', \Auth::id())
            ->where('status', 'pending')
            ->count();

        $approvedCount = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('status', 'approved')
            ->count();

        $rejectedCount = ApprovalRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('status', 'rejected')
            ->count();

        $flows = \App\Models\ApprovalFlow::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->orderBy('name')
            ->get(['id', 'name']);
        $delegates = User::query()
            ->where(function ($query) {
                $query->where('created_by', \Auth::user()->creatorId())
                    ->orWhere('id', \Auth::user()->creatorId());
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        $requestableResources = $this->loadRequestableResources();
        $resourceCatalog = $this->resourceCatalog();

        return view('approval_request.index', compact(
            'requests',
            'status',
            'overdueCount',
            'pendingCount',
            'assignedCount',
            'assigned',
            'approvedCount',
            'rejectedCount',
            'flows',
            'delegates',
            'query',
            'flowId',
            'requestableResources',
            'resourceCatalog'
        ));
    }

    public function store(Request $request)
    {
        if (! \Auth::user()->can('create approval request') && ! \Auth::user()->can('manage approval request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validated = $request->validate([
            'approval_flow_id' => 'nullable|integer|exists:approval_flows,id',
            'resource_target' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        [$resourceKey, $resourceId] = array_pad(explode(':', $validated['resource_target'], 2), 2, null);
        $resourceModel = $this->resolveResourceModel($resourceKey, (int) $resourceId);
        if (! $resourceModel) {
            return redirect()->back()->with('error', __('Selected resource could not be found in your tenant scope.'));
        }

        $resourceType = get_class($resourceModel);
        $ownerId = \Auth::user()->creatorId();
        $flow = null;
        if (! empty($validated['approval_flow_id'])) {
            $flow = ApprovalFlow::query()
                ->where('created_by', $ownerId)
                ->where('id', $validated['approval_flow_id'])
                ->first();

            if (! $flow) {
                return redirect()->back()->with('error', __('Selected approval flow is outside your tenant scope.'));
            }

            if ($flow->resource_type && $flow->resource_type !== $resourceType) {
                return redirect()->back()->with('error', __('Selected approval flow does not support this resource type.'));
            }
        }

        $existingPending = ApprovalRequest::query()
            ->where('created_by', $ownerId)
            ->where('resource_type', $resourceType)
            ->where('resource_id', $resourceModel->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return redirect()->route('approval-requests.show', $existingPending)
                ->with('error', __('A pending approval request already exists for this record.'));
        }

        $approvalRequest = $this->approvalService->createRequest(
            $resourceModel,
            [
                'approval_flow_id' => $flow?->id,
                'resource_type' => $resourceType,
                'amount' => $validated['amount'] ?? null,
            ],
            [
                'triggered_by' => \Auth::id(),
                'owner_id' => $ownerId,
                'event' => 'manual.approval_request',
            ]
        );

        return redirect()->route('approval-requests.show', $approvalRequest)
            ->with('success', __('Approval request submitted successfully.'));
    }

    public function show(ApprovalRequest $approvalRequest)
    {
        $this->ensureOwner($approvalRequest, 'show approval request');
        $approvalRequest->load(['approvalFlow', 'currentStep', 'requester', 'delegatedUser', 'actions.actor', 'actions.step']);
        $delegates = User::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->orWhere('id', \Auth::user()->creatorId())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('approval_request.show', compact('approvalRequest', 'delegates'));
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        $this->ensureOwner($approvalRequest, 'edit approval request');
        $this->approvalService->approve($approvalRequest, \Auth::id(), $request->input('comment'));

        return redirect()->back()->with('success', __('Approval request approved.'));
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest)
    {
        $this->ensureOwner($approvalRequest, 'edit approval request');
        $this->approvalService->reject($approvalRequest, \Auth::id(), $request->input('comment'));

        return redirect()->back()->with('success', __('Approval request rejected.'));
    }

    public function delegate(Request $request, ApprovalRequest $approvalRequest)
    {
        $this->ensureOwner($approvalRequest, 'edit approval request');
        $validated = $request->validate(['delegate_user_id' => 'required|integer|exists:users,id']);
        $delegate = User::query()
            ->where('id', $validated['delegate_user_id'])
            ->where(function ($query) {
                $query->where('created_by', \Auth::user()->creatorId())
                    ->orWhere('id', \Auth::user()->creatorId());
            })
            ->first();
        if (! $delegate) {
            return redirect()->back()->with('error', __('Selected delegate is outside your tenant scope.'));
        }
        $this->approvalService->delegate($approvalRequest, (int) $delegate->id, \Auth::id());

        return redirect()->back()->with('success', __('Approval request delegated.'));
    }

    public function escalate(ApprovalRequest $approvalRequest)
    {
        $this->ensureOwner($approvalRequest, 'edit approval request');
        $this->approvalService->escalateOverdue(\Auth::user()->creatorId());

        return redirect()->back()->with('success', __('Overdue approval requests escalated.'));
    }

    public function escalateAll()
    {
        if (! \Auth::user()->can('edit approval request')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $count = $this->approvalService->escalateOverdue(\Auth::user()->creatorId());

        return redirect()->route('approval-requests.index')
            ->with('success', __('Escalated :count overdue approval request(s).', ['count' => $count]));
    }

    private function ensureOwner(ApprovalRequest $approvalRequest, string $permission): void
    {
        if (! \Auth::user()->can($permission) || (int) $approvalRequest->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }
    }

    private function resourceCatalog(): array
    {
        return [
            'invoice' => ['label' => __('Invoice'), 'model' => Invoice::class],
            'purchase' => ['label' => __('Purchase'), 'model' => Purchase::class],
            'delivery_note' => ['label' => __('Delivery Note'), 'model' => DeliveryNote::class],
            'medical_invoice' => ['label' => __('Medical Invoice'), 'model' => MedicalInvoice::class],
            'insurance_claim' => ['label' => __('Insurance Claim'), 'model' => InsuranceClaim::class],
        ];
    }

    private function loadRequestableResources(): array
    {
        $ownerId = \Auth::user()->creatorId();
        $resources = [];

        foreach ($this->resourceCatalog() as $key => $config) {
            $modelClass = $config['model'];
            $items = $modelClass::query()
                ->where('created_by', $ownerId)
                ->latest('id')
                ->limit(12)
                ->get()
                ->map(fn ($item) => [
                    'value' => $key.':'.$item->id,
                    'label' => $this->resourceLabel($item, $config['label']),
                ])
                ->all();

            if ($items !== []) {
                $resources[$key] = [
                    'label' => $config['label'],
                    'items' => $items,
                ];
            }
        }

        return $resources;
    }

    private function resolveResourceModel(?string $resourceKey, int $resourceId): object|null
    {
        $catalog = $this->resourceCatalog();
        if (! $resourceKey || ! isset($catalog[$resourceKey]) || $resourceId <= 0) {
            return null;
        }

        $modelClass = $catalog[$resourceKey]['model'];

        return $modelClass::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('id', $resourceId)
            ->first();
    }

    private function resourceLabel(object $resource, string $fallbackLabel): string
    {
        foreach (['invoice_id', 'purchase_id', 'delivery_note_number', 'code', 'reference', 'number', 'title', 'name'] as $field) {
            $value = data_get($resource, $field);
            if (filled($value)) {
                return '#'.$resource->id.' · '.$value;
            }
        }

        return '#'.$resource->id.' · '.$fallbackLabel;
    }
}
