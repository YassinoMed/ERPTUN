@extends('layouts.admin')

@section('page-title')
    {{ __('Agriculture Dashboard') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Agriculture Dashboard') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Pilot parcels, crop plans, weather exposure and lot aging from a single agricultural control room.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Parcels') }}</span><strong class="ux-kpi-value">{{ $parcelSummary->total_parcels ?? 0 }}</strong><span class="ux-kpi-meta">{{ __('registered plots') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Managed Area') }}</span><strong class="ux-kpi-value">{{ round($parcelSummary->total_area ?? 0, 2) }}</strong><span class="ux-kpi-meta">{{ __('hectares declared') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Active Plans') }}</span><strong class="ux-kpi-value">{{ $planSummary['in_progress'] ?? 0 }}</strong><span class="ux-kpi-meta">{{ __('campaigns underway') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('High Weather Alerts') }}</span><strong class="ux-kpi-value">{{ $weatherSummary['high'] ?? 0 }}</strong><span class="ux-kpi-meta">{{ __('high severity alerts') }}</span></div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Crop Plan Pipeline') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Planned') }}</span><span>{{ $planSummary['planned'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('In Progress') }}</span><span>{{ $planSummary['in_progress'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between py-2"><span>{{ __('Completed') }}</span><span>{{ $planSummary['completed'] ?? 0 }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Weather Exposure') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Low') }}</span><span>{{ $weatherSummary['low'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Medium') }}</span><span>{{ $weatherSummary['medium'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between py-2"><span>{{ __('High') }}</span><span>{{ $weatherSummary['high'] ?? 0 }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Active Campaigns') }}</h5></div>
                <div class="card-body">
                    @forelse ($activePlans as $plan)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $plan->crop_name }}</strong>
                                <span>{{ ucfirst($plan->status) }}</span>
                            </div>
                            <div class="small text-muted">
                                {{ $plan->variety ?: '-' }} / {{ $plan->start_date?->format('Y-m-d') }} -> {{ $plan->end_date?->format('Y-m-d') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No active crop plans available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Lot Aging / Expiry') }}</h5></div>
                <div class="card-body">
                    @forelse ($lotAging as $lot)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $lot->code }}</strong>
                                <span>{{ optional($lot->expiry_date)->format('Y-m-d') ?: '-' }}</span>
                            </div>
                            <div class="small text-muted">
                                {{ $lot->name }} / {{ $lot->crop_type }} / {{ $lot->quantity }} {{ $lot->unit }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No lots with expiry tracking available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
