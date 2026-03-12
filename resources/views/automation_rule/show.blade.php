@extends('layouts.admin')
@section('page-title', $automationRule->name)
@section('content')
<div class="row">
<div class="col-md-5">
    <div class="card mb-3"><div class="card-header"><h5>{{ __('Rule Definition') }}</h5></div><div class="card-body"><pre class="small">{{ json_encode(['conditions' => $automationRule->conditions, 'actions' => $automationRule->actions], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div></div>
    <div class="card"><div class="card-header"><h5>{{ __('Rule Catalog') }}</h5></div><div class="card-body small">
        <div class="mb-2"><strong>{{ __('Suggested events') }}:</strong> {{ implode(', ', $eventCatalog ?? []) }}</div>
        <div class="mb-2"><strong>{{ __('Suggested fields') }}:</strong> {{ implode(', ', $conditionFields ?? []) }}</div>
        <div><strong>{{ __('Available actions') }}:</strong> {{ implode(', ', array_keys($actionCatalog ?? [])) }}</div>
    </div></div>
</div>
<div class="col-md-7">
    <div class="card mb-3">
        <div class="card-header"><h5>{{ __('Simulation') }}</h5></div>
        <div class="card-body">
            <p class="text-muted small">{{ __('Run the rule again against a previously logged record to validate conditions and actions without editing the rule.') }}</p>
            <form method="POST" action="{{ route('automation-rules.simulate', $automationRule) }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-9">
                    <label class="form-label">{{ __('Execution sample') }}</label>
                    <select name="automation_log_id" class="form-control" required>
                        <option value="">{{ __('Select a previous execution context') }}</option>
                        @foreach($simulationCandidates as $candidate)
                            <option value="{{ $candidate->id }}">#{{ $candidate->id }} · {{ $candidate->event_name }} · {{ $candidate->model_type }}#{{ $candidate->model_id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button class="btn btn-primary" @disabled($simulationCandidates->isEmpty())>{{ __('Simulate') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card"><div class="card-header"><h5>{{ __('Execution Logs') }}</h5></div><div class="card-body"><table class="table"><thead><tr><th>ID</th><th>{{ __('Status') }}</th><th>{{ __('Triggered') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@foreach($logs as $log)<tr><td>#{{ $log->id }}</td><td>{{ ucfirst($log->status) }}</td><td>{{ optional($log->triggered_at)->format('Y-m-d H:i') }}</td><td class="d-flex gap-2">@if(in_array($log->status, ['failed','partial_failed']))<form method="POST" action="{{ route('automation-logs.retry', $log) }}">@csrf <button class="btn btn-sm btn-warning">{{ __('Retry') }}</button></form>@endif</td></tr>@endforeach</tbody></table></div></div>
</div>
</div>
@endsection
