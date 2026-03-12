@extends('layouts.admin')

@section('page-title')
    {{ __('Industrial BI') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Industrial BI') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Review machine utilization, quality mix, schedule risk and industrial cost structure from a BI-oriented control room.') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Planned Machine Hours') }}</small><h4>{{ $kpis['planned_machine_hours'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Actual Machine Hours') }}</small><h4>{{ $kpis['actual_machine_hours'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Downtime Minutes') }}</small><h4>{{ $kpis['downtime_minutes'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Completion Rate') }}</small><h4>{{ $kpis['completion_rate'] }}%</h4></div></div></div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Cost Mix') }}</h5></div>
                <div class="card-body">
                    @forelse ($costMix as $type => $total)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ ucfirst($type) }}</span>
                            <span>{{ \Auth::user()->priceFormat($total) }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No industrial cost data available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Quality Mix') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Pass') }}</span><span>{{ $qualitySummary['pass'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Hold') }}</span><span>{{ $qualitySummary['hold'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between py-2"><span>{{ __('Fail') }}</span><span>{{ $qualitySummary['fail'] ?? 0 }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Top Bottlenecks') }}</h5></div>
                <div class="card-body">
                    @forelse ($topBottlenecks as $center)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <strong>{{ $center->name }}</strong>
                                <div class="small text-muted">{{ $center->resource?->name ?: '-' }}</div>
                            </div>
                            <span>{{ $center->active_orders_count }} {{ __('active orders') }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No bottleneck data available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Schedule Risk') }}</h5></div>
                <div class="card-body">
                    @forelse ($scheduleRisk as $risk)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $risk['order']->order_number }}</strong>
                                <span>{{ $risk['completion'] }}%</span>
                            </div>
                            <div class="small text-muted">
                                {{ $risk['order']->planned_end_date ? \Carbon\Carbon::parse($risk['order']->planned_end_date)->format('Y-m-d') : '-' }}
                                / {{ $risk['late'] ? __('Late') : __('On track') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No schedule risk detected.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
