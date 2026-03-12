@extends('layouts.admin')

@section('page-title', __('Edit Initiative'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppm-portfolios.index') }}">{{ __('Portfolio Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Initiative') }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('ppm-initiatives.update', $ppmInitiative) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">{{ __('Name') }}</label><input type="text" name="name" class="form-control" value="{{ old('name', $ppmInitiative->name) }}" required></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Project') }}</label><select name="project_id" class="form-control"><option value="">{{ __('Linked project') }}</option>@foreach($projects as $projectId => $projectName)<option value="{{ $projectId }}" @selected(old('project_id', $ppmInitiative->project_id) == $projectId)>{{ $projectName }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Sponsor') }}</label><select name="sponsor_id" class="form-control"><option value="">{{ __('Select sponsor') }}</option>@foreach($owners as $ownerId => $ownerName)<option value="{{ $ownerId }}" @selected(old('sponsor_id', $ppmInitiative->sponsor_id) == $ownerId)>{{ $ownerName }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($initiativeStatuses as $statusKey => $statusLabel)<option value="{{ $statusKey }}" @selected(old('status', $ppmInitiative->status) == $statusKey)>{{ __($statusLabel) }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Health') }}</label><select name="health_status" class="form-control">@foreach($healthStatuses as $statusKey => $statusLabel)<option value="{{ $statusKey }}" @selected(old('health_status', $ppmInitiative->health_status) == $statusKey)>{{ __($statusLabel) }}</option>@endforeach</select></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Budget') }}</label><input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $ppmInitiative->budget) }}"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Target') }}</label><input type="number" step="0.01" name="target_value" class="form-control" value="{{ old('target_value', $ppmInitiative->target_value) }}"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Achieved') }}</label><input type="number" step="0.01" name="achieved_value" class="form-control" value="{{ old('achieved_value', $ppmInitiative->achieved_value) }}"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('Start date') }}</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $ppmInitiative->start_date) }}"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">{{ __('End date') }}</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $ppmInitiative->end_date) }}"></div>
                    <div class="col-md-12 mb-3"><label class="form-label">{{ __('Description') }}</label><textarea name="description" class="form-control" rows="4">{{ old('description', $ppmInitiative->description) }}</textarea></div>
                </div>
                <div class="text-end"><a href="{{ route('ppm-portfolios.show', $ppmInitiative->ppm_portfolio_id) }}" class="btn btn-light">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button></div>
            </form>
        </div>
    </div>
@endsection
