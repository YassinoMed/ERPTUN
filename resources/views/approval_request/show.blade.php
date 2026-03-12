@extends('layouts.admin')
@section('page-title', __('Approval Request').' #'.$approvalRequest->id)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('approval-requests.index') }}">{{ __('Approval Requests') }}</a></li>
<li class="breadcrumb-item">{{ '#'.$approvalRequest->id }}</li>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Request Overview') }}</h5></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">{{ __('Flow') }}</dt>
                    <dd class="col-sm-7">{{ optional($approvalRequest->approvalFlow)->name ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Status') }}</dt>
                    <dd class="col-sm-7"><span class="badge bg-light text-dark">{{ ucfirst($approvalRequest->status) }}</span></dd>
                    <dt class="col-sm-5">{{ __('Current Step') }}</dt>
                    <dd class="col-sm-7">{{ optional($approvalRequest->currentStep)->name ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Requested By') }}</dt>
                    <dd class="col-sm-7">{{ optional($approvalRequest->requester)->name ?: ('#'.$approvalRequest->requested_by) }}</dd>
                    <dt class="col-sm-5">{{ __('Delegated To') }}</dt>
                    <dd class="col-sm-7">{{ optional($approvalRequest->delegatedUser)->name ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Due At') }}</dt>
                    <dd class="col-sm-7">{{ optional($approvalRequest->due_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Escalated') }}</dt>
                    <dd class="col-sm-7">{{ optional($approvalRequest->escalated_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Resource') }}</dt>
                    <dd class="col-sm-7">{{ class_basename($approvalRequest->resource_type) }}#{{ $approvalRequest->resource_id }}</dd>
                </dl>
            </div>
        </div>

        @if($approvalRequest->status === 'pending')
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">{{ __('Workflow Actions') }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('approval-requests.approve', $approvalRequest) }}" class="mb-3">
                        @csrf
                        <label class="form-label">{{ __('Approval Comment') }}</label>
                        <textarea name="comment" class="form-control mb-2" rows="2" placeholder="{{ __('Optional comment') }}"></textarea>
                        <button class="btn btn-success">{{ __('Approve') }}</button>
                    </form>
                    <form method="POST" action="{{ route('approval-requests.reject', $approvalRequest) }}" class="mb-3">
                        @csrf
                        <label class="form-label">{{ __('Rejection Reason') }}</label>
                        <textarea name="comment" class="form-control mb-2" rows="2" placeholder="{{ __('Required when configured by the step') }}"></textarea>
                        <button class="btn btn-danger">{{ __('Reject') }}</button>
                    </form>
                    <form method="POST" action="{{ route('approval-requests.delegate', $approvalRequest) }}" class="mb-2">
                        @csrf
                        <label class="form-label">{{ __('Delegate To') }}</label>
                        <select name="delegate_user_id" class="form-control mb-2" required>
                            <option value="">{{ __('Select user') }}</option>
                            @foreach($delegates as $delegate)
                                <option value="{{ $delegate->id }}">{{ $delegate->name }} ({{ $delegate->email }})</option>
                            @endforeach
                        </select>
                        <button class="btn btn-secondary">{{ __('Delegate') }}</button>
                    </form>
                    <form method="POST" action="{{ route('approval-requests.escalate', $approvalRequest) }}">
                        @csrf
                        <button class="btn btn-warning">{{ __('Escalate Overdue Requests') }}</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="col-xl-8">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Context Payload') }}</h5></div>
            <div class="card-body">
                <pre class="small mb-0">{{ json_encode($approvalRequest->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('Action History') }}</h5></div>
            <div class="card-body table-border-style">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Step') }}</th>
                        <th>{{ __('Actor') }}</th>
                        <th>{{ __('Comment') }}</th>
                        <th>{{ __('When') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($approvalRequest->actions as $action)
                        <tr>
                            <td>{{ ucfirst($action->action) }}</td>
                            <td>{{ optional($action->step)->name ?: '-' }}</td>
                            <td>{{ optional($action->actor)->name ?: ('#'.$action->acted_by) }}</td>
                            <td>{{ $action->comment ?: '-' }}</td>
                            <td>{{ optional($action->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">{{ __('No actions recorded yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
