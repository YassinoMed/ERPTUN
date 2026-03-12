@extends('layouts.admin')
@section('page-title', __('Approval Requests'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Approval Requests') }}</li>
@endsection
@section('page-subtitle', __('Track pending approvals, overdue escalations and delegated workload from one queue.'))
@section('content')
<div class="row mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Pending') }}</span><h3 class="mb-0">{{ $pendingCount }}</h3></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Assigned To Me') }}</span><h3 class="mb-0">{{ $assignedCount }}</h3></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Overdue') }}</span><h3 class="mb-0">{{ $overdueCount }}</h3></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Approved') }}</span><h3 class="mb-0">{{ $approvedCount }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Rejected') }}</span><h3 class="mb-0">{{ $rejectedCount }}</h3></div></div></div>
</div>

@if($overdueCount > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ __('Escalation required') }}</strong>
            <div class="small text-muted">{{ __('You have :count overdue requests still pending in the queue.', ['count' => $overdueCount]) }}</div>
        </div>
        <form method="POST" action="{{ route('approval-requests.escalate-all') }}">
            @csrf
            <button class="btn btn-sm btn-warning">{{ __('Run escalation now') }}</button>
        </form>
    </div>
@endif

@if(Auth::user()->can('create approval request') || Auth::user()->can('manage approval request'))
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Create Approval Request') }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('approval-requests.store') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">{{ __('Resource') }}</label>
                    <select name="resource_target" class="form-control" required>
                        <option value="">{{ __('Select a recent record') }}</option>
                        @foreach($requestableResources as $group)
                            <optgroup label="{{ $group['label'] }}">
                                @foreach($group['items'] as $item)
                                    <option value="{{ $item['value'] }}">{{ $item['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Approval Flow') }}</label>
                    <select name="approval_flow_id" class="form-control">
                        <option value="">{{ __('Auto-resolve') }}</option>
                        @foreach($flows as $flow)
                            <option value="{{ $flow->id }}">{{ $flow->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Amount Override') }}</label>
                    <input type="number" min="0" step="0.01" name="amount" class="form-control" placeholder="0.00">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">{{ __('Submit') }}</button>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('approval-requests.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">{{ __('Search') }}</label>
                <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="{{ __('Flow, requester, delegate or resource reference') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('Flow') }}</label>
                <select name="flow" class="form-control">
                    <option value="">{{ __('All flows') }}</option>
                    @foreach($flows as $flow)
                        <option value="{{ $flow->id }}" @selected((string) $flowId === (string) $flow->id)>{{ $flow->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-control">
                    <option value="">{{ __('All statuses') }}</option>
                    <option value="pending" @selected($status === 'pending')>{{ __('Pending') }}</option>
                    <option value="approved" @selected($status === 'approved')>{{ __('Approved') }}</option>
                    <option value="rejected" @selected($status === 'rejected')>{{ __('Rejected') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="assigned" value="1" id="assignedFilter" @checked($assigned)>
                    <label class="form-check-label" for="assignedFilter">{{ __('Only mine') }}</label>
                </div>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary">{{ __('Apply') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
<div class="card-body table-border-style"><div class="table-responsive">
<table class="table">
    <thead><tr><th>#</th><th>{{ __('Flow') }}</th><th>{{ __('Requester') }}</th><th>{{ __('Status') }}</th><th>{{ __('Current Step') }}</th><th>{{ __('Delegated To') }}</th><th>{{ __('Due') }}</th><th>{{ __('Action') }}</th></tr></thead>
    <tbody>
    @foreach($requests as $request)
        <tr>
            <td>#{{ $request->id }}</td>
            <td>{{ optional($request->approvalFlow)->name ?: '-' }}</td>
            <td>{{ optional($request->requester)->name ?: ('#'.$request->requested_by) }}</td>
            <td>
                <span class="badge {{ $request->status === 'pending' ? 'bg-warning text-dark' : ($request->status === 'approved' ? 'bg-success' : 'bg-danger') }}">
                    {{ ucfirst($request->status) }}
                </span>
            </td>
            <td>{{ optional($request->currentStep)->name ?: '-' }}</td>
            <td>{{ optional($request->delegatedUser)->name ?: '-' }}</td>
            <td>
                {{ optional($request->due_at)->format('Y-m-d H:i') ?: '-' }}
                @if($request->status === 'pending' && $request->due_at && $request->due_at->isPast())
                    <div class="small text-danger">{{ __('Overdue') }}</div>
                @endif
            </td>
            <td>
                <a href="{{ route('approval-requests.show', $request) }}" class="btn btn-sm btn-light mb-1">{{ __('Open') }}</a>
                @if($request->status === 'pending')
                    <div class="d-flex gap-2 flex-wrap">
                        <form method="POST" action="{{ route('approval-requests.approve', $request) }}">@csrf <input type="hidden" name="comment" value="Approved from dashboard"><button class="btn btn-sm btn-success">{{ __('Approve') }}</button></form>
                        <form method="POST" action="{{ route('approval-requests.reject', $request) }}">@csrf <input type="text" name="comment" class="form-control form-control-sm mb-1" placeholder="{{ __('Reject reason') }}"><button class="btn btn-sm btn-danger">{{ __('Reject') }}</button></form>
                        <form method="POST" action="{{ route('approval-requests.delegate', $request) }}">@csrf <select name="delegate_user_id" class="form-control form-control-sm mb-1" required><option value="">{{ __('Delegate to') }}</option>@foreach($delegates as $delegate)<option value="{{ $delegate->id }}">{{ $delegate->name }}</option>@endforeach</select><button class="btn btn-sm btn-secondary">{{ __('Delegate') }}</button></form>
                        <form method="POST" action="{{ route('approval-requests.escalate', $request) }}">@csrf <button class="btn btn-sm btn-warning">{{ __('Escalate') }}</button></form>
                    </div>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $requests->links() }}
</div></div></div>
@endsection
