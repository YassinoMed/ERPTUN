@extends('layouts.admin')

@section('page-title')
    {{ __('Customer Portal') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('retail.operations.index') }}">{{ __('Retail Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Customer Portal') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Review customer invoices, recoveries, contracts and deliveries from an internal self-service perspective.') }}
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('retail.customer-portal') }}" class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">{{ __('Customer') }}</label>
                    <select name="customer_id" class="form-control">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(optional($selectedCustomer)->id === $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">{{ __('Open Portal View') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedCustomer)
        <div class="ux-kpi-grid mb-4">
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Invoices') }}</span><strong class="ux-kpi-value">{{ $invoices->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Open recoveries') }}</span><strong class="ux-kpi-value">{{ $recoveries->where('status', '!=', 'closed')->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Delivery notes') }}</span><strong class="ux-kpi-value">{{ $deliveryNotes->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Contracts') }}</span><strong class="ux-kpi-value">{{ $contracts->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Invoice exposure') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat(data_get($customerSummary, 'invoice_due', 0)) }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Matching promotions') }}</span><strong class="ux-kpi-value">{{ data_get($customerSummary, 'promotion_matches', 0) }}</strong></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Invoices') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Invoice') }}</th><th>{{ __('Issue date') }}</th><th>{{ __('Total') }}</th><th>{{ __('Due') }}</th></tr></thead>
                            <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</td>
                                    <td>{{ $invoice->issue_date ? \Auth::user()->dateFormat($invoice->issue_date) : '-' }}</td>
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
                    <div class="card-header"><h5>{{ __('Recoveries') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Stage') }}</th><th>{{ __('Priority') }}</th><th>{{ __('Due') }}</th></tr></thead>
                            <tbody>
                            @foreach($recoveries as $recovery)
                                <tr>
                                    <td>{{ $recovery->reference }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $recovery->stage)) }}</td>
                                    <td>{{ ucfirst($recovery->priority) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($recovery->due_amount) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Deliveries') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Date') }}</th><th>{{ __('Tracking') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @foreach($deliveryNotes as $note)
                                <tr>
                                    <td>{{ $note->reference ?: ('#' . $note->id) }}</td>
                                    <td>{{ $note->delivery_date?->format('Y-m-d') }}</td>
                                    <td>{{ $note->tracking_number ?: '-' }}</td>
                                    <td>{{ ucfirst($note->status) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Contracts') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Contract') }}</th><th>{{ __('Cycle') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $contract->billing_cycle ?: 'one_off')) }}</td>
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
