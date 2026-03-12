@extends('layouts.admin')
@section('page-title', __('Edit Key Result'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('okr-objectives.index') }}">{{ __('OKR Workspace') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Key Result') }}</li>
@endsection
@section('content')
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('okr-key-results.update', $okrKeyResult) }}">@csrf @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">{{ __('Metric') }}</label><input type="text" name="metric_name" class="form-control" value="{{ old('metric_name', $okrKeyResult->metric_name) }}" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">{{ __('Start') }}</label><input type="number" step="0.01" name="start_value" class="form-control" value="{{ old('start_value', $okrKeyResult->start_value) }}"></div>
            <div class="col-md-2 mb-3"><label class="form-label">{{ __('Target') }}</label><input type="number" step="0.01" name="target_value" class="form-control" value="{{ old('target_value', $okrKeyResult->target_value) }}" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">{{ __('Current') }}</label><input type="number" step="0.01" name="current_value" class="form-control" value="{{ old('current_value', $okrKeyResult->current_value) }}"></div>
            <div class="col-md-4 mb-3"><label class="form-label">{{ __('Unit') }}</label><input type="text" name="unit" class="form-control" value="{{ old('unit', $okrKeyResult->unit) }}"></div>
            <div class="col-md-4 mb-3"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($keyResultStatuses as $statusKey => $statusLabel)<option value="{{ $statusKey }}" @selected(old('status', $okrKeyResult->status) == $statusKey)>{{ __($statusLabel) }}</option>@endforeach</select></div>
            <div class="col-md-4 mb-3"><label class="form-label">{{ __('Due date') }}</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', $okrKeyResult->due_date) }}"></div>
        </div>
        <div class="text-end"><a href="{{ route('okr-objectives.show', $okrKeyResult->okr_objective_id) }}" class="btn btn-light">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button></div></form></div></div>
@endsection
