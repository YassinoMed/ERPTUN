@extends('layouts.admin')

@section('page-title')
    {{ __('Commercial BI') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('retail.operations.index') }}">{{ __('Retail Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Commercial BI') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track retail network health, contract exposure, procurement load and top selling items from one distribution analytics board.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Stores') }}</span><strong class="ux-kpi-value">{{ $scoreboard['store_count'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('POS sessions') }}</span><strong class="ux-kpi-value">{{ $scoreboard['sessions_count'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Transactions') }}</span><strong class="ux-kpi-value">{{ $scoreboard['transactions_count'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Mixed payment sessions') }}</span><strong class="ux-kpi-value">{{ $scoreboard['mixed_payment_sessions'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Promotion budget') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat($scoreboard['promotion_budget']) }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Contract exposure') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat($scoreboard['contract_exposure']) }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Procurement backlog') }}</span><strong class="ux-kpi-value">{{ $scoreboard['procurement_backlog'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Replenishment backlog') }}</span><strong class="ux-kpi-value">{{ $scoreboard['replenishment_backlog'] }}</strong></div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Store Network') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Store') }}</th><th>{{ __('Type') }}</th><th>{{ __('Target revenue') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($stores as $store)
                            <tr>
                                <td>{{ $store->name }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $store->store_type ?? 'store')) }}</td>
                                <td>{{ \Auth::user()->priceFormat($store->target_revenue) }}</td>
                                <td>{{ ucfirst($store->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Promotion Portfolio') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Promotion') }}</th><th>{{ __('Store') }}</th><th>{{ __('Audience') }}</th><th>{{ __('Budget') }}</th></tr></thead>
                        <tbody>
                        @foreach($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->name }}</td>
                                <td>{{ optional($promotion->retailStore)->name ?: __('Global') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $promotion->audience_type ?? 'all')) }}</td>
                                <td>{{ \Auth::user()->priceFormat($promotion->budget_amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Top Sales Products') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Product') }}</th><th>{{ __('Qty sold') }}</th><th>{{ __('Revenue') }}</th></tr></thead>
                        <tbody>
                        @foreach($invoiceItems as $item)
                            <tr>
                                <td>{{ optional($item->product)->name ?: ('#' . $item->product_id) }}</td>
                                <td>{{ $item->quantity_sold }}</td>
                                <td>{{ \Auth::user()->priceFormat($item->total_revenue) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Top Purchase Products') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Product') }}</th><th>{{ __('Qty bought') }}</th><th>{{ __('Spend') }}</th></tr></thead>
                        <tbody>
                        @foreach($purchaseItems as $item)
                            <tr>
                                <td>{{ optional($item->product)->name ?: ('#' . $item->product_id) }}</td>
                                <td>{{ $item->quantity_bought }}</td>
                                <td>{{ \Auth::user()->priceFormat($item->spend_total) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
