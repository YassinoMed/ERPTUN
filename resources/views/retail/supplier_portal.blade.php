@extends('layouts.admin')

@section('page-title')
    {{ __('Supplier Portal') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('retail.operations.index') }}">{{ __('Retail Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Supplier Portal') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track purchase exposure and supplier commercial agreements through one supplier-facing workspace.') }}
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('retail.supplier-portal') }}" class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">{{ __('Supplier') }}</label>
                    <select name="vender_id" class="form-control">
                        @foreach($venders as $vender)
                            <option value="{{ $vender->id }}" @selected(optional($selectedVender)->id === $vender->id)>{{ $vender->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">{{ __('Open Portal View') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedVender)
        <div class="ux-kpi-grid mb-4">
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Purchases') }}</span><strong class="ux-kpi-value">{{ $purchases->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Open due') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat($purchases->sum(fn($purchase) => $purchase->getDue())) }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Contracts') }}</span><strong class="ux-kpi-value">{{ $contracts->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Purchase exposure') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat(data_get($supplierSummary, 'purchase_total', 0)) }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Monthly contracts') }}</span><strong class="ux-kpi-value">{{ data_get($supplierSummary, 'monthly_contracts', 0) }}</strong></div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Purchases') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Purchase') }}</th><th>{{ __('Date') }}</th><th>{{ __('Total') }}</th><th>{{ __('Due') }}</th></tr></thead>
                            <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->purchase_number ?: ('#' . $purchase->id) }}</td>
                                    <td>{{ $purchase->purchase_date ? \Auth::user()->dateFormat($purchase->purchase_date) : '-' }}</td>
                                    <td>{{ \Auth::user()->priceFormat($purchase->getTotal()) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($purchase->getDue()) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Commercial Contracts') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Contract') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ \Auth::user()->priceFormat($contract->amount) }}</td>
                                    <td>{{ ucfirst($contract->status) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
