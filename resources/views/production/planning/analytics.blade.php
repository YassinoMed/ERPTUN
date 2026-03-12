@extends('layouts.admin')

@section('page-title')
    {{ __('Industrial Analytics') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Industrial Analytics') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track completion, labor pressure and shopfloor delays in a single reporting view.') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Order Completion') }}</h5></div>
                <div class="card-body">
                    @forelse ($orderMetrics as $metric)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $metric['order']->order_number }}</strong>
                                <span>{{ $metric['completion'] }}%</span>
                            </div>
                            <div class="small text-muted">
                                {{ $metric['order']->product?->name ?: '-' }} /
                                {{ $metric['order']->workCenter?->name ?: '-' }} /
                                {{ $metric['order']->quantity_produced }} / {{ $metric['order']->quantity_planned }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No production orders to analyze.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Labor Pressure') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Operator') }}</th>
                                <th>{{ __('Hours') }}</th>
                                <th>{{ __('Logs') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laborPerformance as $item)
                                <tr>
                                    <td>{{ $item->employee_name }}</td>
                                    <td>{{ round($item->total_minutes / 60, 2) }}</td>
                                    <td>{{ $item->log_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ __('No labor logs available.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Operation Delays') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Operation') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Work Center') }}</th>
                                <th>{{ __('Variance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($operationDelays as $operation)
                                <tr>
                                    <td>{{ $operation->name }}</td>
                                    <td>{{ $operation->productionOrder?->order_number ?: '-' }}</td>
                                    <td>{{ $operation->workCenter?->name ?: '-' }}</td>
                                    <td>{{ max(((int) $operation->actual_minutes - (int) $operation->planned_minutes), 0) }} {{ __('min') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">{{ __('No delayed operations found.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Shopfloor Timeline') }}</h5></div>
                <div class="card-body">
                    @forelse ($shopfloorTimeline as $event)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event->workCenter?->name ?: '-' }}</strong>
                                <span>{{ $event->happened_at?->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="small text-muted">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }} /
                                {{ $event->status }} /
                                {{ $event->order?->product?->name ?: __('No product') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No shopfloor timeline available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Quality Overview') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Pass') }}</span>
                        <span>{{ $qualitySummary['pass'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Hold') }}</span>
                        <span>{{ $qualitySummary['hold'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Fail') }}</span>
                        <span>{{ $qualitySummary['fail'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Maintenance Impact') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Open') }}</span>
                        <span>{{ $maintenanceImpact['open'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('In Progress') }}</span>
                        <span>{{ $maintenanceImpact['in_progress'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Closed') }}</span>
                        <span>{{ $maintenanceImpact['closed'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
