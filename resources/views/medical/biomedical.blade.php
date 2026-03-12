@extends('layouts.admin')

@section('page-title')
    {{ __('Biomedical Assets') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical.operations.index') }}">{{ __('Advanced Medical Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Biomedical') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Manage maintenance readiness, calibration due dates and asset availability for clinical equipment.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Operational') }}</span><strong class="ux-kpi-value">{{ $kpis['operational'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('In maintenance') }}</span><strong class="ux-kpi-value">{{ $kpis['maintenance'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Calibration due') }}</span><strong class="ux-kpi-value">{{ $kpis['due'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Out of service') }}</span><strong class="ux-kpi-value">{{ $kpis['out_of_service'] }}</strong></div>
    </div>

    <div class="card">
        <div class="card-header"><h5>{{ __('Asset Readiness') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Asset') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Location') }}</th>
                        <th>{{ __('Calibration due') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Update') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biomedicalAssets as $asset)
                        <tr>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->asset_code }}</td>
                            <td>{{ $asset->equipment_type ?: '-' }}</td>
                            <td>{{ $asset->location ?: '-' }}</td>
                            <td>{{ $asset->calibration_due_date?->format('Y-m-d') ?: '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $asset->maintenance_status)) }}</td>
                            <td>
                                <form action="{{ route('medical.operations.biomedical-assets.status', $asset->id) }}" method="post" class="d-flex gap-2">
                                    @csrf
                                    <select name="maintenance_status" class="form-control form-control-sm">
                                        <option value="operational" @selected($asset->maintenance_status === 'operational')>{{ __('Operational') }}</option>
                                        <option value="maintenance" @selected($asset->maintenance_status === 'maintenance')>{{ __('Maintenance') }}</option>
                                        <option value="due" @selected($asset->maintenance_status === 'due')>{{ __('Due') }}</option>
                                        <option value="out_of_service" @selected($asset->maintenance_status === 'out_of_service')>{{ __('Out of Service') }}</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary">{{ __('Save') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
