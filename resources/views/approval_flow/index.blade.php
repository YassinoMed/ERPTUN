@extends('layouts.admin')
@section('page-title', __('Approval Flows'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Approval Flows') }}</li>
@endsection
@section('action-btn')
<a href="{{ route('approval-flows.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>
@endsection
@section('content')
<div class="card"><div class="card-body table-border-style"><div class="table-responsive">
<table class="table">
    <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Resource') }}</th><th>{{ __('Steps') }}</th><th>{{ __('SLA') }}</th><th>{{ __('Action') }}</th></tr></thead>
    <tbody>
    @forelse($flows as $flow)
        <tr>
            <td>{{ $flow->name }}</td>
            <td>{{ $flow->resource_type ?: __('Any') }}</td>
            <td>{{ $flow->steps_count }}</td>
            <td>{{ $flow->default_sla_hours ?: '-' }}</td>
            <td class="d-flex gap-2">
                <a class="btn btn-sm btn-warning" href="{{ route('approval-flows.show', $flow) }}">{{ __('View') }}</a>
                <a class="btn btn-sm btn-info" href="{{ route('approval-flows.edit', $flow) }}">{{ __('Edit') }}</a>
                <form method="POST" action="{{ route('approval-flows.destroy', $flow) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">{{ __('Delete') }}</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-muted">{{ __('No approval flows found.') }}</td></tr>
    @endforelse
    </tbody>
</table>
</div></div></div>
@endsection
