@extends('layouts.admin')
@section('page-title', __('Automation Rules'))
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item">{{ __('Automation Rules') }}</li>@endsection
@section('action-btn')<a href="{{ route('automation-rules.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endsection
@section('content')
<div class="row mb-3">
<div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Rules') }}</span><h3 class="mb-0">{{ $stats['rules'] }}</h3></div></div></div>
<div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Active') }}</span><h3 class="mb-0">{{ $stats['active'] }}</h3></div></div></div>
<div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Triggered') }}</span><h3 class="mb-0">{{ $stats['triggered'] }}</h3></div></div></div>
<div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Failed Logs') }}</span><h3 class="mb-0">{{ $stats['failedLogs'] }}</h3></div></div></div>
</div>
<div class="card mb-3"><div class="card-body table-border-style"><div class="table-responsive"><table class="table">
<thead><tr><th>{{ __('Name') }}</th><th>{{ __('Event') }}</th><th>{{ __('Status') }}</th><th>{{ __('Priority') }}</th><th>{{ __('Last Triggered') }}</th><th>{{ __('Action') }}</th></tr></thead>
<tbody>
@forelse($rules as $rule)
<tr><td>{{ $rule->name }}</td><td><code>{{ $rule->event_name }}</code></td><td><span class="badge {{ $rule->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $rule->is_active ? __('Active') : __('Paused') }}</span></td><td>{{ $rule->priority }}</td><td>{{ optional($rule->last_triggered_at)->diffForHumans() ?: '-' }}</td><td class="d-flex gap-2 flex-wrap"><a class="btn btn-sm btn-warning" href="{{ route('automation-rules.show', $rule) }}">{{ __('View') }}</a><a class="btn btn-sm btn-info" href="{{ route('automation-rules.edit', $rule) }}">{{ __('Edit') }}</a><form method="POST" action="{{ route('automation-rules.toggle-status', $rule) }}">@csrf <button class="btn btn-sm btn-secondary">{{ $rule->is_active ? __('Pause') : __('Activate') }}</button></form><form method="POST" action="{{ route('automation-rules.duplicate', $rule) }}">@csrf <button class="btn btn-sm btn-light">{{ __('Duplicate') }}</button></form><form method="POST" action="{{ route('automation-rules.destroy', $rule) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">{{ __('Delete') }}</button></form></td></tr>
@empty
<tr><td colspan="6" class="text-muted">{{ __('No automation rules found.') }}</td></tr>
@endforelse
</tbody></table></div></div></div>

<div class="card">
    <div class="card-header"><h5 class="mb-0">{{ __('Recent Failed Executions') }}</h5></div>
    <div class="card-body table-border-style">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>{{ __('Rule') }}</th><th>{{ __('Event') }}</th><th>{{ __('Status') }}</th><th>{{ __('Triggered') }}</th><th>{{ __('Action') }}</th></tr></thead>
                <tbody>
                @forelse($recentFailedLogs as $log)
                    <tr>
                        <td>{{ optional($log->automationRule)->name ?: ('#'.$log->automation_rule_id) }}</td>
                        <td><code>{{ $log->event_name }}</code></td>
                        <td>{{ ucfirst($log->status) }}</td>
                        <td>{{ optional($log->triggered_at)->diffForHumans() ?: '-' }}</td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($log->automationRule)
                                    <a class="btn btn-sm btn-warning" href="{{ route('automation-rules.show', $log->automationRule) }}">{{ __('Open Rule') }}</a>
                                @endif
                                <form method="POST" action="{{ route('automation-logs.retry', $log) }}">@csrf <button class="btn btn-sm btn-primary">{{ __('Retry') }}</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">{{ __('No failed automation execution found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
