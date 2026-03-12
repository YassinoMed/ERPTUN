@extends('layouts.admin')

@section('page-title')
    {{ __('Shopfloor Realtime') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Shopfloor Realtime') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Monitor live work center status, current order flow and recent plant events from a single realtime board.') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Active Orders') }}</small><h4>{{ $activeOrders->count() }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Status Events (8h)') }}</small><h4>{{ $summary['status'] ?? 0 }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Downtime Events (8h)') }}</small><h4>{{ $summary['downtime'] ?? 0 }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><small>{{ __('Quality Holds (8h)') }}</small><h4>{{ $summary['quality_hold'] ?? 0 }}</h4></div></div></div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Live Work Center Board') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Work Center') }}</th>
                                <th>{{ __('Resource') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Event') }}</th>
                                <th>{{ __('Updated At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($liveBoard as $row)
                                <tr>
                                    <td>{{ $row['work_center']->name }}</td>
                                    <td>{{ $row['work_center']->resource?->name ?: '-' }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</span></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $row['event_type'])) }}</td>
                                    <td>{{ $row['happened_at'] ? $row['happened_at']->format('Y-m-d H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">{{ __('No live work center activity available.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Current Orders') }}</h5></div>
                <div class="card-body">
                    @forelse ($activeOrders as $order)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $order->order_number }}</strong>
                                <span>{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="small text-muted">
                                {{ $order->product?->name ?: '-' }} /
                                {{ $order->workCenter?->name ?: '-' }} /
                                {{ $order->quantity_produced }} / {{ $order->quantity_planned }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No active orders found.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Recent Event Timeline') }}</h5></div>
                <div class="card-body">
                    @forelse ($timeline as $event)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $event->workCenter?->name ?: '-' }}</strong>
                                <span>{{ $event->happened_at?->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="small text-muted">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }} /
                                {{ $event->status }} /
                                {{ $event->order?->order_number ?: __('No order') }} /
                                {{ $event->employee?->name ?: __('No operator') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No shopfloor timeline available.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
