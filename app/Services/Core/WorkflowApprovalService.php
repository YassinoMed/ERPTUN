<?php

namespace App\Services\Core;

use App\Models\ApprovalAction;
use App\Models\ApprovalFlow;
use App\Models\ApprovalRequest;
use App\Models\ApprovalStep;
use App\Models\WorkflowDelegation;

class WorkflowApprovalService
{
    public function __construct(
        private readonly TimelineService $timeline
    ) {
    }

    public function createRequest($model, array $data = [], array $context = []): ApprovalRequest
    {
        $ownerId = (int) ($context['owner_id'] ?? $model->created_by ?? 0);
        $amount = $this->resolveAmount($data, $context, $model);
        $resourceType = $data['resource_type'] ?? get_class($model);

        $flow = $this->resolveFlow($ownerId, $resourceType, $amount, $data['approval_flow_id'] ?? null);
        $steps = $flow ? $this->resolveSteps($flow, $amount) : collect();
        $currentStep = $steps->first();

        $request = ApprovalRequest::create([
            'approval_flow_id' => $flow?->id,
            'current_step_id' => $currentStep?->id,
            'resource_type' => $resourceType,
            'resource_id' => $model->id ?? null,
            'status' => $currentStep ? 'pending' : 'approved',
            'requested_by' => $context['triggered_by'] ?? null,
            'delegated_to' => $this->resolveDelegateId($currentStep, $ownerId, $flow?->allow_delegation ?? false),
            'due_at' => $this->resolveDueAt($flow, $currentStep),
            'context' => [
                'amount' => $amount,
                'event' => $context['event'] ?? null,
                'workflow_name' => $context['workflow_name'] ?? null,
                'steps' => $steps->pluck('id')->values()->all(),
            ],
            'created_by' => $ownerId,
        ]);

        $this->timeline->record(
            $model,
            'Approval request created',
            $flow ? 'Approval flow '.$flow->name.' initialized.' : 'Approval request auto-approved.',
            [
                'approval_request_id' => $request->id,
                'approval_flow_id' => $flow?->id,
                'current_step_id' => $currentStep?->id,
            ],
            'approval',
            $ownerId,
            $context['triggered_by'] ?? null
        );

        return $request;
    }

    public function approve(ApprovalRequest $request, ?int $actorId = null, ?string $comment = null): ApprovalRequest
    {
        $currentStep = $request->currentStep;
        $steps = collect($request->context['steps'] ?? []);
        $currentIndex = $steps->search($request->current_step_id);
        $nextStepId = $currentIndex === false ? null : $steps->get($currentIndex + 1);
        $nextStep = $nextStepId ? ApprovalStep::find($nextStepId) : null;

        ApprovalAction::create([
            'approval_request_id' => $request->id,
            'approval_step_id' => $request->current_step_id,
            'action' => 'approved',
            'comment' => $comment,
            'acted_by' => $actorId,
            'metadata' => ['next_step_id' => $nextStep?->id],
            'created_by' => $request->created_by,
        ]);

        $request->current_step_id = $nextStep?->id;
        $request->delegated_to = $this->resolveDelegateId($nextStep, $request->created_by, $request->approvalFlow?->allow_delegation ?? false);
        $request->due_at = $nextStep ? $this->resolveDueAt($request->approvalFlow, $nextStep) : null;
        $request->status = $nextStep ? 'pending' : 'approved';
        $request->save();

        return $request->refresh();
    }

    public function reject(ApprovalRequest $request, ?int $actorId = null, ?string $comment = null): ApprovalRequest
    {
        $currentStep = $request->currentStep;
        if ($currentStep && $currentStep->require_reject_reason && blank($comment)) {
            throw new \InvalidArgumentException('A rejection reason is required for this approval step.');
        }

        ApprovalAction::create([
            'approval_request_id' => $request->id,
            'approval_step_id' => $request->current_step_id,
            'action' => 'rejected',
            'comment' => $comment,
            'acted_by' => $actorId,
            'metadata' => ['rejected_at' => now()->toIso8601String()],
            'created_by' => $request->created_by,
        ]);

        $request->status = 'rejected';
        $request->rejection_reason = $comment;
        $request->save();

        return $request->refresh();
    }

    public function delegate(ApprovalRequest $request, int $delegateUserId, int $actorId): ApprovalRequest
    {
        ApprovalAction::create([
            'approval_request_id' => $request->id,
            'approval_step_id' => $request->current_step_id,
            'action' => 'delegated',
            'comment' => 'Delegated approval request.',
            'acted_by' => $actorId,
            'metadata' => ['delegated_to' => $delegateUserId],
            'created_by' => $request->created_by,
        ]);

        $request->delegated_to = $delegateUserId;
        $request->save();

        return $request->refresh();
    }

    public function escalateOverdue(?int $ownerId = null): int
    {
        $query = ApprovalRequest::query()
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->whereNull('escalated_at')
            ->where('due_at', '<=', now())
            ->with(['approvalFlow', 'currentStep']);

        if ($ownerId) {
            $query->where('created_by', $ownerId);
        }

        $count = 0;
        foreach ($query->get() as $request) {
            $escalationUserId = $request->currentStep?->escalation_user_id ?: $request->approvalFlow?->escalation_user_id;
            if ($escalationUserId) {
                $request->delegated_to = $escalationUserId;
            }
            $request->escalated_at = now();
            $request->save();

            ApprovalAction::create([
                'approval_request_id' => $request->id,
                'approval_step_id' => $request->current_step_id,
                'action' => 'escalated',
                'comment' => 'Approval request escalated automatically.',
                'acted_by' => null,
                'metadata' => ['delegated_to' => $escalationUserId],
                'created_by' => $request->created_by,
            ]);

            $count++;
        }

        return $count;
    }

    private function resolveFlow(int $ownerId, string $resourceType, ?float $amount, ?int $flowId = null): ?ApprovalFlow
    {
        $query = ApprovalFlow::query()
            ->where('created_by', $ownerId)
            ->where('is_active', true)
            ->where(function ($builder) use ($resourceType) {
                $builder->whereNull('resource_type')->orWhere('resource_type', $resourceType);
            });

        if ($amount !== null) {
            $query->where(function ($builder) use ($amount) {
                $builder->whereNull('min_amount')->orWhere('min_amount', '<=', $amount);
            })->where(function ($builder) use ($amount) {
                $builder->whereNull('max_amount')->orWhere('max_amount', '>=', $amount);
            });
        }

        if ($flowId) {
            $query->where('id', $flowId);
        }

        return $query->latest('id')->first();
    }

    private function resolveSteps(?ApprovalFlow $flow, ?float $amount)
    {
        if (! $flow) {
            return collect();
        }

        return $flow->steps()
            ->when($amount !== null, function ($query) use ($amount) {
                $query->where(function ($builder) use ($amount) {
                    $builder->whereNull('threshold_min')->orWhere('threshold_min', '<=', $amount);
                })->where(function ($builder) use ($amount) {
                    $builder->whereNull('threshold_max')->orWhere('threshold_max', '>=', $amount);
                });
            })
            ->orderBy('sequence')
            ->get();
    }

    private function resolveAmount(array $data, array $context, $model): ?float
    {
        $amount = $data['amount'] ?? $context['amount'] ?? data_get($model, 'amount');

        return is_numeric($amount) ? (float) $amount : null;
    }

    private function resolveDelegateId(?ApprovalStep $step, int $ownerId, bool $allowDelegation): ?int
    {
        if (! $step || ! $allowDelegation || ! $step->approver_id) {
            return $step?->approver_id;
        }

        $delegation = WorkflowDelegation::query()
            ->where('created_by', $ownerId)
            ->where('user_id', $step->approver_id)
            ->where('is_active', true)
            ->get()
            ->first(fn (WorkflowDelegation $item) => $item->isEffective());

        return $delegation?->delegate_user_id ?: $step->approver_id;
    }

    private function resolveDueAt(?ApprovalFlow $flow, ?ApprovalStep $step)
    {
        $hours = $step?->sla_hours ?: $flow?->default_sla_hours;

        return $hours ? now()->addHours((int) $hours) : null;
    }
}
