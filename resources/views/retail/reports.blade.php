@extends('layouts.admin')

@section('page-title')
    {{ __('Retail Reports') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('retail.operations.index') }}">{{ __('Retail Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Reports') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track store activation, promotion pressure, contract exposure and recent commercial flow.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Active stores') }}</span><strong class="ux-kpi-value">{{ $kpis['active_stores'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Open sessions') }}</span><strong class="ux-kpi-value">{{ $kpis['open_sessions'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Active promotions') }}</span><strong class="ux-kpi-value">{{ $kpis['active_promotions'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Contract value') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat($kpis['contract_value']) }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Payment mix') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat(data_get($paymentMix, 'payments_total', 0)) }}</strong></div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Recent Sales') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Customer') }}</th><th>{{ __('Invoice') }}</th><th>{{ __('Total') }}</th><th>{{ __('Due') }}</th></tr></thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ optional($invoice->customer)->name ?: '-' }}</td>
                                <td>{{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</td>
                                <td>{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</td>
                                <td>{{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Recent Purchases') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Supplier') }}</th><th>{{ __('Purchase') }}</th><th>{{ __('Total') }}</th><th>{{ __('Due') }}</th></tr></thead>
                        <tbody>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ optional($purchase->vender)->name ?: '-' }}</td>
                                <td>{{ $purchase->purchase_number ?: ('#' . $purchase->id) }}</td>
                                <td>{{ \Auth::user()->priceFormat($purchase->getTotal()) }}</td>
                                <td>{{ \Auth::user()->priceFormat($purchase->getDue()) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Promotion Pipeline') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Code') }}</th><th>{{ __('Name') }}</th><th>{{ __('Type') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->code }}</td>
                                <td>{{ $promotion->name }}</td>
                                <td>{{ ucfirst($promotion->promotion_type) }}</td>
                                <td>{{ ucfirst($promotion->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Commercial Contracts') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Number') }}</th><th>{{ __('Party') }}</th><th>{{ __('Cycle') }}</th><th>{{ __('Amount') }}</th></tr></thead>
                        <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td>{{ $contract->contract_number }}</td>
                                <td>{{ $contract->party_name ?: ucfirst($contract->party_type) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $contract->billing_cycle ?: 'one_off')) }}</td>
                                <td>{{ \Auth::user()->priceFormat($contract->amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Store Performance') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Store') }}</th><th>{{ __('Sessions') }}</th><th>{{ __('Transactions') }}</th><th>{{ __('Variance') }}</th></tr></thead>
                        <tbody>
                        @foreach($storePerformance as $row)
                            <tr>
                                <td>{{ $row['store']->name }}</td>
                                <td>{{ $row['sessions'] }}</td>
                                <td>{{ $row['transactions'] }}</td>
                                <td>{{ \Auth::user()->priceFormat($row['variance']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Procurement Pipeline') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Store') }}</th><th>{{ __('Budget') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($procurementRequests as $request)
                            <tr>
                                <td>{{ $request->reference }}</td>
                                <td>{{ optional($request->retailStore)->name ?: '-' }}</td>
                                <td>{{ \Auth::user()->priceFormat($request->budget_amount) }}</td>
                                <td>{{ ucfirst($request->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Replenishment Flow') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Product') }}</th><th>{{ __('To') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($replenishments as $request)
                            <tr>
                                <td>{{ optional($request->product)->name ?: '-' }}</td>
                                <td>{{ optional($request->destinationStore)->name ?: '-' }}</td>
                                <td>{{ $request->suggested_quantity }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $request->status)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h5>{{ __('Top Products By Sales') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Product') }}</th><th>{{ __('Sold Qty') }}</th></tr></thead>
                        <tbody>
                        @foreach($topProducts as $item)
                            <tr>
                                <td>{{ optional($item->product)->name ?: ('#' . $item->product_id) }}</td>
                                <td>{{ $item->sold_quantity }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
