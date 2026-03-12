@extends('layouts.admin')

@section('page-title', $ppmPortfolio->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppm-portfolios.index') }}">{{ __('Portfolio Management') }}</a></li>
    <li class="breadcrumb-item">{{ $ppmPortfolio->name }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('edit ppm portfolio')
            <a href="{{ route('ppm-portfolios.edit', $ppmPortfolio) }}" class="btn btn-sm btn-info me-2"><i class="ti ti-pencil"></i></a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Initiatives') }}</span><strong class="ux-kpi-value">{{ $ppmPortfolio->initiatives->count() }}</strong><span class="ux-kpi-meta">{{ __('portfolio workload') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Budget') }}</span><strong class="ux-kpi-value">{{ Auth::user()->priceFormat($ppmPortfolio->initiatives->sum('budget')) }}</strong><span class="ux-kpi-meta">{{ __('tracked across initiatives') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('At risk') }}</span><strong class="ux-kpi-value">{{ $ppmPortfolio->initiatives->where('health_status', 'red')->count() }}</strong><span class="ux-kpi-meta">{{ __('items needing intervention') }}</span></div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ __('Portfolio Summary') }}</h5>
                    <p class="text-muted mb-2">{{ $ppmPortfolio->description ?: __('No description provided.') }}</p>
                    <div><strong>{{ __('Owner') }}:</strong> {{ optional($ppmPortfolio->owner)->name ?: '-' }}</div>
                    <div><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $ppmPortfolio->status))) }}</div>
                    <div><strong>{{ __('Priority') }}:</strong> {{ $ppmPortfolio->priority ?: '-' }}</div>
                </div>
            </div>

            @can('create ppm initiative')
                <div class="card">
                    <div class="card-body">
                        <h5>{{ __('Add Initiative') }}</h5>
                        <form method="POST" action="{{ route('ppm-initiatives.store', $ppmPortfolio) }}">
                            @csrf
                            <div class="mb-3"><label class="form-label">{{ __('Name') }}</label><input type="text" name="name" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label">{{ __('Project') }}</label><select name="project_id" class="form-control"><option value="">{{ __('Linked project') }}</option>@foreach($projects as $projectId => $projectName)<option value="{{ $projectId }}">{{ $projectName }}</option>@endforeach</select></div>
                            <div class="mb-3"><label class="form-label">{{ __('Sponsor') }}</label><select name="sponsor_id" class="form-control"><option value="">{{ __('Select sponsor') }}</option>@foreach($owners as $ownerId => $ownerName)<option value="{{ $ownerId }}">{{ $ownerName }}</option>@endforeach</select></div>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($initiativeStatuses as $statusKey => $statusLabel)<option value="{{ $statusKey }}">{{ __($statusLabel) }}</option>@endforeach</select></div>
                                <div class="col-md-6 mb-3"><label class="form-label">{{ __('Health') }}</label><select name="health_status" class="form-control">@foreach($healthStatuses as $statusKey => $statusLabel)<option value="{{ $statusKey }}">{{ __($statusLabel) }}</option>@endforeach</select></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="form-label">{{ __('Budget') }}</label><input type="number" step="0.01" name="budget" class="form-control"></div>
                                <div class="col-md-6 mb-3"><label class="form-label">{{ __('Achieved') }}</label><input type="number" step="0.01" name="achieved_value" class="form-control"></div>
                            </div>
                            <div class="mb-3"><label class="form-label">{{ __('Target value') }}</label><input type="number" step="0.01" name="target_value" class="form-control"></div>
                            <div class="mb-3"><label class="form-label">{{ __('Description') }}</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                            <button type="submit" class="btn btn-primary">{{ __('Add initiative') }}</button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
        <div class="col-lg-8">
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Initiative') }}</th><th>{{ __('Sponsor') }}</th><th>{{ __('Delivery') }}</th><th>{{ __('Budget') }}</th><th>{{ __('Action') }}</th></tr></thead>
                            <tbody>
                            @forelse($ppmPortfolio->initiatives as $initiative)
                                <tr>
                                    <td><div>{{ $initiative->name }}</div><small class="text-muted">{{ optional($initiative->project)->project_name ?: __('No linked project') }}</small></td>
                                    <td>{{ optional($initiative->sponsor)->name ?: '-' }}</td>
                                    <td><span class="badge bg-info">{{ __(ucfirst(str_replace('_', ' ', $initiative->status))) }}</span> <span class="badge bg-secondary">{{ __(ucfirst($initiative->health_status)) }}</span></td>
                                    <td>{{ Auth::user()->priceFormat($initiative->budget) }}</td>
                                    <td class="Action">
                                        @can('edit ppm initiative')
                                            <div class="action-btn me-2"><a href="{{ route('ppm-initiatives.edit', $initiative) }}" class="mx-3 btn btn-sm bg-info"><i class="ti ti-pencil text-white"></i></a></div>
                                        @endcan
                                        @can('delete ppm initiative')
                                            <div class="action-btn">
                                                <form method="POST" action="{{ route('ppm-initiatives.destroy', $initiative) }}" id="delete-initiative-{{ $initiative->id }}">@csrf @method('DELETE')
                                                    <a href="#" class="mx-3 btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-initiative-{{ $initiative->id }}').submit();"><i class="ti ti-trash text-white"></i></a>
                                                </form>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">{{ __('No initiatives created yet.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
