@extends('layouts.admin')
@section('page-title', $okrObjective->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('okr-objectives.index') }}">{{ __('OKR Workspace') }}</a></li>
    <li class="breadcrumb-item">{{ $okrObjective->title }}</li>
@endsection
@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Progress') }}</span><strong class="ux-kpi-value">{{ number_format((float) $okrObjective->progress, 0) }}%</strong><span class="ux-kpi-meta">{{ __('objective completion') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Key Results') }}</span><strong class="ux-kpi-value">{{ $okrObjective->keyResults->count() }}</strong><span class="ux-kpi-meta">{{ __('tracked metrics') }}</span></div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card"><div class="card-body"><h5>{{ __('Objective Summary') }}</h5><p class="text-muted mb-2">{{ $okrObjective->description ?: __('No description provided.') }}</p><div><strong>{{ __('Owner') }}:</strong> {{ optional($okrObjective->owner)->name ?: '-' }}</div><div><strong>{{ __('Cycle') }}:</strong> {{ $okrObjective->cycle ?: '-' }}</div><div><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $okrObjective->status))) }}</div></div></div>
            @can('create okr key result')
                <div class="card"><div class="card-body"><h5>{{ __('Add Key Result') }}</h5><form method="POST" action="{{ route('okr-key-results.store', $okrObjective) }}">@csrf
                    <div class="mb-3"><label class="form-label">{{ __('Metric') }}</label><input type="text" name="metric_name" class="form-control" required></div>
                    <div class="row"><div class="col-md-4 mb-3"><label class="form-label">{{ __('Start') }}</label><input type="number" step="0.01" name="start_value" class="form-control" value="0"></div><div class="col-md-4 mb-3"><label class="form-label">{{ __('Target') }}</label><input type="number" step="0.01" name="target_value" class="form-control" required></div><div class="col-md-4 mb-3"><label class="form-label">{{ __('Current') }}</label><input type="number" step="0.01" name="current_value" class="form-control" value="0"></div></div>
                    <div class="row"><div class="col-md-6 mb-3"><label class="form-label">{{ __('Unit') }}</label><input type="text" name="unit" class="form-control"></div><div class="col-md-6 mb-3"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($keyResultStatuses as $statusKey => $statusLabel)<option value="{{ $statusKey }}">{{ __($statusLabel) }}</option>@endforeach</select></div></div>
                    <div class="mb-3"><label class="form-label">{{ __('Due date') }}</label><input type="date" name="due_date" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">{{ __('Add key result') }}</button></form></div></div>
            @endcan
        </div>
        <div class="col-lg-8">
            <div class="card ux-list-card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table"><thead><tr><th>{{ __('Metric') }}</th><th>{{ __('Progress') }}</th><th>{{ __('Status') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@forelse($okrObjective->keyResults as $keyResult)<tr><td><div>{{ $keyResult->metric_name }}</div><small class="text-muted">{{ $keyResult->unit ?: __('No unit') }}</small></td><td>{{ $keyResult->current_value }} / {{ $keyResult->target_value }}</td><td><span class="badge bg-info">{{ __(ucfirst(str_replace('_', ' ', $keyResult->status))) }}</span></td><td class="Action">@can('edit okr key result')<div class="action-btn me-2"><a href="{{ route('okr-key-results.edit', $keyResult) }}" class="mx-3 btn btn-sm bg-info"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete okr key result')<div class="action-btn"><form method="POST" action="{{ route('okr-key-results.destroy', $keyResult) }}" id="delete-key-result-{{ $keyResult->id }}">@csrf @method('DELETE')<a href="#" class="mx-3 btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-key-result-{{ $keyResult->id }}').submit();"><i class="ti ti-trash text-white"></i></a></form></div>@endcan</td></tr>@empty<tr><td colspan="4" class="text-center text-muted">{{ __('No key results created yet.') }}</td></tr>@endforelse</tbody></table></div></div></div>
        </div>
    </div>
@endsection
