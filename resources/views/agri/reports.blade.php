@extends('layouts.admin')

@section('page-title')
    {{ __('Agri Reports') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Agri Reports') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Monitor quality release, FEFO pressure, transformation yield and export mix.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Pass checks') }}</span>
            <strong class="ux-kpi-value">{{ $qualitySummary['pass'] ?? 0 }}</strong>
            <span class="ux-kpi-meta">{{ __('released lots') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Warnings') }}</span>
            <strong class="ux-kpi-value">{{ $qualitySummary['warning'] ?? 0 }}</strong>
            <span class="ux-kpi-meta">{{ __('lots under review') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Fails') }}</span>
            <strong class="ux-kpi-value">{{ $qualitySummary['fail'] ?? 0 }}</strong>
            <span class="ux-kpi-meta">{{ __('blocked lots') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('FEFO queue') }}</span>
            <strong class="ux-kpi-value">{{ $fefoQueue->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('stored lots ordered by expiry') }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Destination Mix') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Country') }}</th><th>{{ __('Shipped quantity') }}</th></tr></thead>
                        <tbody>
                            @forelse($destinationSummary as $item)
                                <tr>
                                    <td>{{ $item->destination_country }}</td>
                                    <td>{{ $item->total_quantity }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">{{ __('No shipment data available.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Transformation Yield by Process') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Process') }}</th><th>{{ __('Input') }}</th><th>{{ __('Output') }}</th><th>{{ __('Waste') }}</th></tr></thead>
                        <tbody>
                            @forelse($transformationYield as $item)
                                <tr>
                                    <td>{{ $item->process_step }}</td>
                                    <td>{{ $item->total_input }}</td>
                                    <td>{{ $item->total_output }}</td>
                                    <td>{{ $item->total_waste }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">{{ __('No transformation data available.') }}</td></tr>
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
                <div class="card-header"><h5 class="mb-0">{{ __('Origin / Parcel Mix') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Origin') }}</th><th>{{ __('Lots') }}</th><th>{{ __('Quantity') }}</th></tr></thead>
                        <tbody>
                            @forelse($sourceSummary as $item)
                                <tr>
                                    <td>{{ $item->parcel_origin ?: __('Unknown') }}</td>
                                    <td>{{ $item->total_lots }}</td>
                                    <td>{{ $item->total_quantity }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ __('No origin data available.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Cold Chain Status') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Stored') }}</span><span>{{ $coldChainSummary['stored'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ __('Released') }}</span><span>{{ $coldChainSummary['released'] ?? 0 }}</span></div>
                    <div class="d-flex justify-content-between py-2"><span>{{ __('Blocked') }}</span><span>{{ $coldChainSummary['blocked'] ?? 0 }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('FEFO Queue') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead><tr><th>{{ __('Lot') }}</th><th>{{ __('Facility') }}</th><th>{{ __('Expiry') }}</th><th>{{ __('Qty') }}</th></tr></thead>
                <tbody>
                    @forelse($fefoQueue as $record)
                        <tr>
                            <td>{{ optional($record->lot)->code ?? '-' }}</td>
                            <td>{{ $record->facility_name }}</td>
                            <td>{{ optional($record->expiry_date)->format('Y-m-d') }}</td>
                            <td>{{ $record->quantity }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">{{ __('No FEFO queue available.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('Cooperative Supply Volume') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead><tr><th>{{ __('Cooperative ID') }}</th><th>{{ __('Delivered Qty') }}</th></tr></thead>
                <tbody>
                    @forelse($cooperativeSummary as $item)
                        <tr>
                            <td>{{ $item->cooperative_id }}</td>
                            <td>{{ $item->total_net_weight }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted">{{ __('No cooperative delivery data available.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
