@extends('layouts.admin')
@section('page-title', __('Tenant Onboarding'))
@section('page-subtitle', __('Guide tenant setup, monitor quota pressure and activate paid add-ons from a single cockpit.'))
@section('content')
<div class="row">
<div class="col-md-5">
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">{{ __('Subscription Overview') }}</h5></div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="text-muted small">{{ __('Current plan') }}</div>
                    <h4 class="mb-0">{{ $currentPlan->name ?? __('No active plan') }}</h4>
                    <div class="small text-muted">{{ $currentPlan ? $currentPlan->price.' / '.$currentPlan->duration : __('Assign a plan to unlock quotas and modules.') }}</div>
                </div>
                @if($pendingPlanRequest)
                    <span class="badge bg-warning text-dark">{{ __('Pending request') }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('core.onboarding.plan-request.store') }}" class="row g-2">
                @csrf
                <div class="col-md-7">
                    <label class="form-label">{{ __('Request plan change') }}</label>
                    <select name="plan_id" class="form-control" required>
                        <option value="">{{ __('Select target plan') }}</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} · {{ $plan->price }} / {{ $plan->duration }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">{{ __('Internal note') }}</label>
                    <input type="text" name="request_note" class="form-control" placeholder="{{ __('Why change plan?') }}">
                </div>
                <div class="col-12 d-grid">
                    <button class="btn btn-outline-primary" @disabled($pendingPlanRequest)>{{ $pendingPlanRequest ? __('Plan request already pending') : __('Submit plan request') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <span class="text-muted d-block small">{{ __('Completion progress') }}</span>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h3 class="mb-0">{{ $progress }}%</h3>
                <span class="badge bg-light text-dark">{{ count($checklist->completed_steps ?? []) }}/{{ count($checklist->checklist ?? []) }}</span>
            </div>
            <div class="progress" style="height:10px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            @if($nextSteps->isNotEmpty())
                <div class="mt-3">
                    <div class="text-muted small mb-2">{{ __('Next recommended steps') }}</div>
                    <ul class="mb-0 ps-3">
                        @foreach($nextSteps as $step)
                            <li>{{ $step['label'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Activation Checklist') }}</h5>
            <form method="POST" action="{{ route('core.onboarding.reset') }}">@csrf <button class="btn btn-sm btn-light">{{ __('Reset assistant') }}</button></form>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('core.onboarding.update') }}">
                @csrf
                @foreach(($checklist->checklist ?? []) as $item)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="completed_steps[]" value="{{ $item['key'] }}" {{ in_array($item['key'], $checklist->completed_steps ?? []) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $item['label'] }}</label>
                    </div>
                @endforeach
                <button class="btn btn-primary mt-2">{{ __('Save Checklist') }}</button>
            </form>
        </div>
    </div>
</div>
<div class="col-md-7">
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>{{ __('Tenant Usage & Active Addons') }}</h5>
            <form method="POST" action="{{ route('core.usages.sync') }}">
                @csrf
                <button class="btn btn-sm btn-outline-primary">{{ __('Sync quotas from plan') }}</button>
            </form>
        </div>
        <div class="card-body">
            @if($quotaAlerts->isNotEmpty())
                <div class="alert alert-warning">
                    <strong>{{ __('Quota pressure detected') }}</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($quotaAlerts as $alert)
                            <li>{{ $alert['metric_key'] }}: {{ $alert['usage_value'] }} / {{ $alert['limit_value'] }} ({{ $alert['percent'] }}%)</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h6>{{ __('Usages') }}</h6>
            <table class="table mb-4">
                <thead><tr><th>{{ __('Metric') }}</th><th>{{ __('Usage') }}</th><th>{{ __('Limit') }}</th><th>{{ __('Health') }}</th></tr></thead>
                <tbody>
                @forelse($usageSummary as $usage)
                    <tr>
                        <td>{{ $usage['metric_key'] }}</td>
                        <td>{{ $usage['usage_value'] }}</td>
                        <td>{{ $usage['limit_value'] ?: '-' }}</td>
                        <td>
                            @if(!is_null($usage['percent']))
                                <div class="progress" style="height:8px;"><div class="progress-bar {{ $usage['percent'] >= 90 ? 'bg-danger' : ($usage['percent'] >= 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $usage['percent'] }}%"></div></div>
                                <div class="small text-muted mt-1">{{ $usage['percent'] }}%</div>
                            @else
                                <span class="text-muted">{{ __('Unlimited') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">{{ __('No usage records.') }}</td></tr>
                @endforelse
                </tbody>
            </table>

            <h6>{{ __('Update quota snapshot') }}</h6>
            <form method="POST" action="{{ route('core.usages.store') }}" class="row g-2 mb-4">
                @csrf
                <div class="col-md-4"><input type="text" name="metric_key" class="form-control" placeholder="{{ __('Metric key') }}" required></div>
                <div class="col-md-3"><input type="number" step="0.01" name="usage_value" class="form-control" placeholder="{{ __('Usage') }}" required></div>
                <div class="col-md-3"><input type="number" step="0.01" name="limit_value" class="form-control" placeholder="{{ __('Limit') }}"></div>
                <div class="col-md-2 d-grid"><button class="btn btn-light">{{ __('Save') }}</button></div>
            </form>

            <h6>{{ __('Active Addons') }}</h6>
            <ul class="list-group">
                @forelse($activeAddons as $subscription)
                    <li class="list-group-item d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <strong>{{ optional($subscription->addon)->name ?: __('Addon') }}</strong>
                            <div class="small text-muted">{{ ucfirst($subscription->status) }} • {{ $subscription->billing_cycle ?: optional($subscription->addon)->billing_cycle ?: __('N/A') }}</div>
                            <div class="small text-muted">{{ __('Renews') }}: {{ optional($subscription->renews_at)->format('Y-m-d') ?: '-' }}</div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($subscription->status === 'active')
                                <form method="POST" action="{{ route('core.addons.renew', $subscription) }}">@csrf <button class="btn btn-sm btn-outline-primary">{{ __('Renew') }}</button></form>
                                <form method="POST" action="{{ route('core.addons.deactivate', $subscription) }}">@csrf <button class="btn btn-sm btn-outline-danger">{{ __('Deactivate') }}</button></form>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('No active addons.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">{{ __('Plan Request History') }}</h5></div>
        <div class="card-body table-border-style">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Requested by') }}</th>
                        <th>{{ __('Current') }}</th>
                        <th>{{ __('Target') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Review') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($planRequests as $planRequest)
                    <tr>
                        <td>
                            <div>{{ optional($planRequest->user)->name ?: $planRequest->user_id }}</div>
                            @if($planRequest->request_note)
                                <div class="small text-muted">{{ $planRequest->request_note }}</div>
                            @endif
                        </td>
                        <td>{{ optional($planRequest->currentPlan)->name ?: __('N/A') }}</td>
                        <td>{{ optional($planRequest->plan)->name ?: __('N/A') }}</td>
                        <td><span class="badge bg-light text-dark">{{ ucfirst($planRequest->status) }}</span></td>
                        <td>
                            @if($planRequest->reviewer)
                                <div>{{ $planRequest->reviewer->name }}</div>
                                <div class="small text-muted">{{ optional($planRequest->reviewed_at)->format('Y-m-d H:i') }}</div>
                                @if($planRequest->review_note)
                                    <div class="small text-muted">{{ $planRequest->review_note }}</div>
                                @endif
                            @else
                                <span class="text-muted">{{ __('Pending review') }}</span>
                            @endif
                        </td>
                        <td>
                            @if(\Auth::user()->type === 'super admin' && $planRequest->status === 'pending')
                                <div class="d-flex gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('core.onboarding.plan-requests.approve', $planRequest) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">{{ __('Approve') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('core.onboarding.plan-requests.reject', $planRequest) }}">
                                        @csrf
                                        <input type="hidden" name="review_note" value="{{ __('Rejected from onboarding cockpit') }}">
                                        <button class="btn btn-sm btn-outline-danger">{{ __('Reject') }}</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted">{{ ucfirst($planRequest->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">{{ __('No plan requests recorded yet.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('Recent Subscription Billing Events') }}</h5></div>
        <div class="card-body table-border-style">
            <table class="table">
                <thead><tr><th>{{ __('Order') }}</th><th>{{ __('Plan') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th>{{ __('When') }}</th></tr></thead>
                <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td><code>{{ $order->order_id }}</code></td>
                        <td>{{ $order->plan_name ?: '-' }}</td>
                        <td>{{ $order->price }} {{ $order->price_currency }}</td>
                        <td>{{ ucfirst($order->payment_status) }}</td>
                        <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">{{ __('No billing events found for this tenant yet.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
