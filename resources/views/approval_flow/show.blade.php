@extends('layouts.admin')
@section('page-title', $approvalFlow->name)
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('approval-flows.index') }}">{{ __('Approval Flows') }}</a></li>
<li class="breadcrumb-item">{{ $approvalFlow->name }}</li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card"><div class="card-header"><h5>{{ __('Steps') }}</h5></div><div class="card-body">
            <ul class="list-group">
                @foreach($approvalFlow->steps as $step)
                    <li class="list-group-item">
                        <strong>{{ $step->sequence }}. {{ $step->name }}</strong><br>
                        <small>{{ __('Approver') }}: {{ $step->approver_type ?: '-' }} #{{ $step->approver_id ?: '-' }} | {{ __('SLA') }}: {{ $step->sla_hours ?: '-' }}h</small>
                    </li>
                @endforeach
            </ul>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-header"><h5>{{ __('Recent Requests') }}</h5></div><div class="card-body">
            <table class="table"><thead><tr><th>ID</th><th>{{ __('Status') }}</th><th>{{ __('Due') }}</th></tr></thead><tbody>
                @forelse($requests as $request)
                    <tr><td>#{{ $request->id }}</td><td>{{ ucfirst($request->status) }}</td><td>{{ optional($request->due_at)->format('Y-m-d H:i') ?: '-' }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-muted">{{ __('No requests yet.') }}</td></tr>
                @endforelse
            </tbody></table>
        </div></div>
    </div>
</div>
@endsection
