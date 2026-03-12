@extends('layouts.admin')
@section('page-title')
    {{ __('Industrial Planning') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Industrial Planning') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Balance machine load, labor load and shopfloor execution from a single industrial board.') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Active Orders') }}</small><h4>{{ $orders->count() }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Active Work Centers') }}</small><h4>{{ $workCenters->count() }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Open Maintenance') }}</small><h4>{{ $maintenanceSummary['open'] ?? 0 }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Subcontract In Progress') }}</small><h4>{{ $subcontractSummary['in_progress'] ?? 0 }}</h4></div></div></div>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Capacity Board') }}</h5></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Work Center') }}</th><th>{{ __('Resource') }}</th><th>{{ __('Hours / Day') }}</th><th>{{ __('Workers') }}</th><th>{{ __('Active Orders') }}</th><th>{{ __('Bottleneck') }}</th></tr></thead>
                            <tbody>
                                @foreach ($workCenters as $workCenter)
                                    <tr>
                                        <td>{{ $workCenter->name }}</td>
                                        <td>{{ $workCenter->resource?->name ?: '-' }}</td>
                                        <td>{{ $workCenter->capacity_hours_per_day }}</td>
                                        <td>{{ $workCenter->capacity_workers }}</td>
                                        <td>{{ $workCenter->active_orders_count }}</td>
                                        <td>{{ $workCenter->is_bottleneck ? __('Yes') : __('No') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">{{ __('Machine Load') }}</h5><a href="{{ route('production.planning.bi') }}" class="btn btn-sm btn-outline-primary">{{ __('BI View') }}</a></div>
                <div class="card-body">
                    @forelse ($machineLoadSummary as $metric)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $metric['name'] }}</strong>
                                <span>{{ $metric['utilization_percent'] }}%</span>
                            </div>
                            <div class="small text-muted">
                                {{ __('Planned') }}: {{ $metric['planned_hours'] }}h /
                                {{ __('Actual') }}: {{ $metric['actual_hours'] }}h /
                                {{ __('Downtime') }}: {{ $metric['downtime_minutes'] }} {{ __('min') }} /
                                {{ __('Gap') }}: {{ $metric['load_gap_hours'] }}h /
                                {{ __('State') }}: {{ ucfirst($metric['saturation_status']) }}
                            </div>
                            <div class="small text-muted">{{ __('Downtime rate') }}: {{ $metric['downtime_rate'] }}%</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No machine load data available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Industrial Cost Mix') }}</h5></div>
                <div class="card-body">
                    @foreach ($costSummary as $costType => $total)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ ucfirst($costType) }}</span>
                            <span>{{ \Auth::user()->priceFormat($total) }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No cost records found.') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Shift Teams') }}</h5></div>
                <div class="card-body">
                    @foreach ($shiftTeams as $shiftTeam)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $shiftTeam->name }}</span>
                            <span>{{ $shiftTeam->start_time ?: '--:--' }} - {{ $shiftTeam->end_time ?: '--:--' }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No shift teams configured.') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Shopfloor Capture') }}</h5></div>
                <div class="card-body">
                    <form method="post" action="{{ route('production.planning.shopfloor-events.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Work Center') }}</label>
                            <select name="production_work_center_id" class="form-control" required>
                                <option value="">{{ __('Select work center') }}</option>
                                @foreach ($workCenters as $workCenter)
                                    <option value="{{ $workCenter->id }}">{{ $workCenter->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Order') }}</label>
                            <select name="production_order_id" class="form-control">
                                <option value="">{{ __('Select order') }}</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Event Type') }}</label>
                                <select name="event_type" class="form-control" required>
                                    <option value="status">{{ __('Status') }}</option>
                                    <option value="downtime">{{ __('Downtime') }}</option>
                                    <option value="output">{{ __('Output') }}</option>
                                    <option value="quality_hold">{{ __('Quality Hold') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <input type="text" name="status" class="form-control" required placeholder="{{ __('running / blocked / closed') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Quantity') }}</label>
                                <input type="number" step="0.001" name="quantity" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Downtime') }}</label>
                                <input type="number" name="downtime_minutes" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Happened At') }}</label>
                            <input type="datetime-local" name="happened_at" class="form-control" required value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Capture Event') }}</button>
                        <a href="{{ route('production.planning.realtime') }}" class="btn btn-outline-dark">{{ __('Realtime') }}</a>
                        <a href="{{ route('production.planning.analytics') }}" class="btn btn-outline-primary">{{ __('Analytics') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Labor Load') }}</h5></div>
                <div class="card-body">
                    @forelse ($laborLoadSummary as $metric)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <strong>{{ $metric['name'] }}</strong>
                                <div class="small text-muted">{{ $metric['workers'] }} {{ __('workers') }}</div>
                            </div>
                            <div class="text-end">
                                <div>{{ $metric['planned_hours'] }}h / {{ $metric['actual_hours'] }}h</div>
                                <div class="small text-muted">{{ $metric['hours_per_worker'] }}h/{{ __('worker') }} / {{ __('Gap') }} {{ $metric['gap_hours'] }}h / {{ $metric['utilization_percent'] }}%</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No labor load data available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Recent Shopfloor Events') }}</h5></div>
                <div class="card-body">
                    <div class="small text-muted mb-3">
                        {{ __('24h summary') }}:
                        {{ __('Status') }} {{ $shopfloorSummary['status'] ?? 0 }},
                        {{ __('Downtime') }} {{ $shopfloorSummary['downtime'] ?? 0 }},
                        {{ __('Output') }} {{ $shopfloorSummary['output'] ?? 0 }},
                        {{ __('Quality Hold') }} {{ $shopfloorSummary['quality_hold'] ?? 0 }}
                    </div>
                    @forelse ($shopfloorEvents as $event)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event->workCenter?->name ?: '-' }}</strong>
                                <span>{{ $event->happened_at?->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="small text-muted">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }} /
                                {{ $event->status }} /
                                {{ $event->order?->order_number ?: __('No order') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No shopfloor events captured yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
