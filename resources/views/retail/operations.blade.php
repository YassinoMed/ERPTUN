@extends('layouts.admin')

@section('page-title')
    {{ __('Retail Operations') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Retail Operations') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Run stores, sessions, promotions, contracts and customer-facing retail coordination from one distribution cockpit.') }}
@endsection

@section('action-button')
    <div class="d-flex gap-2">
        <a href="{{ route('retail.operations.reports') }}" class="btn btn-sm btn-primary">{{ __('Retail Reports') }}</a>
        <a href="{{ route('retail.operations.bi') }}" class="btn btn-sm btn-outline-primary">{{ __('Commercial BI') }}</a>
        <a href="{{ route('retail.customer-portal') }}" class="btn btn-sm btn-outline-primary">{{ __('Customer Portal') }}</a>
        <a href="{{ route('retail.supplier-portal') }}" class="btn btn-sm btn-outline-primary">{{ __('Supplier Portal') }}</a>
    </div>
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Open cash registers') }}</span>
            <strong class="ux-kpi-value">{{ $cashRegisters->where('status', 'open')->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('active tills right now') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Open POS sessions') }}</span>
            <strong class="ux-kpi-value">{{ $posSessions->where('status', 'open')->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('store sessions still running') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Retail stores') }}</span>
            <strong class="ux-kpi-value">{{ $retailStores->where('status', 'active')->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('active points of sale') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Active promotions') }}</span>
            <strong class="ux-kpi-value">{{ $promotions->where('status', 'active')->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('campaigns live in stores') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Procurement backlog') }}</span>
            <strong class="ux-kpi-value">{{ $procurementRequests->whereIn('status', ['pending', 'approved', 'ordered'])->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('retail demand waiting for sourcing') }}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Cash Register') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.cash-registers.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Location') }}</label>
                                <input type="text" name="location" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Opening balance') }}</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="open">{{ __('Open') }}</option>
                                    <option value="closed">{{ __('Closed') }}</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Register') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('POS Session') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.pos-sessions.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Store') }}</label>
                                <select name="retail_store_id" class="form-control">
                                    <option value="">{{ __('Select store') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Register') }}</label>
                                <select name="cash_register_id" class="form-control">
                                    <option value="">{{ __('Select register') }}</option>
                                    @foreach($cashRegisters as $register)
                                        <option value="{{ $register->id }}">{{ $register->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Opened at') }}</label>
                                <input type="datetime-local" name="opened_at" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Closed at') }}</label>
                                <input type="datetime-local" name="closed_at" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Expected') }}</label>
                                <input type="number" step="0.01" name="expected_amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Actual') }}</label>
                                <input type="number" step="0.01" name="actual_amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="open">{{ __('Open') }}</option>
                                    <option value="closed">{{ __('Closed') }}</option>
                                    <option value="reconciled">{{ __('Reconciled') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Transactions') }}</label>
                                <input type="number" name="transactions_count" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Session mode') }}</label>
                                <select name="session_mode" class="form-control">
                                    <option value="counter">{{ __('Counter') }}</option>
                                    <option value="mobile">{{ __('Mobile') }}</option>
                                    <option value="self_checkout">{{ __('Self checkout') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Mixed payments') }}</label>
                                <select name="mixed_payment_enabled" class="form-control">
                                    <option value="1">{{ __('Enabled') }}</option>
                                    <option value="0">{{ __('Disabled') }}</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Session') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Retail Store') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.retail-stores.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Store name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Store code') }}</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Region') }}</label>
                                <input type="text" name="region" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Manager') }}</label>
                                <input type="text" name="manager_name" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Type') }}</label>
                                <select name="store_type" class="form-control">
                                    <option value="store">{{ __('Store') }}</option>
                                    <option value="hq">{{ __('Head office') }}</option>
                                    <option value="kiosk">{{ __('Kiosk') }}</option>
                                    <option value="warehouse_hub">{{ __('Warehouse hub') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Parent store') }}</label>
                                <select name="parent_store_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Warehouse') }}</label>
                                <select name="warehouse_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Revenue target') }}</label>
                                <input type="number" step="0.01" name="target_revenue" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Margin target %') }}</label>
                                <input type="number" step="0.01" name="target_margin" class="form-control" value="0">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Store') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Commercial Contract') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.contracts.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Contract number') }}</label>
                                <input type="text" name="contract_number" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Title') }}</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Party type') }}</label>
                                <select name="party_type" class="form-control">
                                    <option value="customer">{{ __('Customer') }}</option>
                                    <option value="vender">{{ __('Supplier') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Customer') }}</label>
                                <select name="party_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                    @foreach($venders as $vender)
                                        <option value="{{ $vender->id }}">{{ $vender->name }} ({{ __('Supplier') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Amount') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Store') }}</label>
                                <select name="retail_store_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Category') }}</label>
                                <input type="text" name="category" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Owner') }}</label>
                                <input type="text" name="owner_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Billing cycle') }}</label>
                                <select name="billing_cycle" class="form-control">
                                    <option value="one_off">{{ __('One off') }}</option>
                                    <option value="monthly">{{ __('Monthly') }}</option>
                                    <option value="quarterly">{{ __('Quarterly') }}</option>
                                    <option value="yearly">{{ __('Yearly') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Renewal notice days') }}</label>
                                <input type="number" name="renewal_notice_days" class="form-control" value="30">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Start') }}</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('End') }}</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Contract') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Cash Movement') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.cash-movements.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Register') }}</label>
                                <select name="cash_register_id" class="form-control" required>
                                    @foreach($cashRegisters as $register)
                                        <option value="{{ $register->id }}">{{ $register->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Type') }}</label>
                                <select name="type" class="form-control">
                                    <option value="in">{{ __('In') }}</option>
                                    <option value="out">{{ __('Out') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Amount') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Date') }}</label>
                                <input type="date" name="movement_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Reference') }}</label>
                                <input type="text" name="reference" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Record Movement') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Loyalty Account') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.loyalty-accounts.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Customer') }}</label>
                                <select name="customer_id" class="form-control" required>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Code') }}</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Points') }}</label>
                                <input type="number" name="points_balance" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Tier') }}</label>
                                <input type="text" name="tier" class="form-control" value="standard">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Loyalty') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Promotion') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.promotions.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Code') }}</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Type') }}</label>
                                <select name="promotion_type" class="form-control">
                                    <option value="discount">{{ __('Discount') }}</option>
                                    <option value="bundle">{{ __('Bundle') }}</option>
                                    <option value="cashback">{{ __('Cashback') }}</option>
                                    <option value="gift">{{ __('Gift') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Scope') }}</label>
                                <select name="scope_type" class="form-control">
                                    <option value="global">{{ __('Global') }}</option>
                                    <option value="store">{{ __('Store') }}</option>
                                    <option value="customer">{{ __('Customer') }}</option>
                                    <option value="product">{{ __('Product') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Discount') }}</label>
                                <input type="number" step="0.01" name="discount_value" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Store') }}</label>
                                <select name="retail_store_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Audience') }}</label>
                                <select name="audience_type" class="form-control">
                                    <option value="all">{{ __('All') }}</option>
                                    <option value="vip">{{ __('VIP') }}</option>
                                    <option value="wholesale">{{ __('Wholesale') }}</option>
                                    <option value="new_customers">{{ __('New customers') }}</option>
                                    <option value="loyalty">{{ __('Loyalty members') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Minimum basket') }}</label>
                                <input type="number" step="0.01" name="minimum_amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Budget') }}</label>
                                <input type="number" step="0.01" name="budget_amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Auto apply') }}</label>
                                <select name="auto_apply" class="form-control">
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Promotion') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Delivery Route') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.delivery-routes.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Route name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Delivery note') }}</label>
                                <select name="delivery_note_id" class="form-control">
                                    <option value="">{{ __('Select delivery note') }}</option>
                                    @foreach($deliveryNotes as $note)
                                        <option value="{{ $note->id }}">{{ \Auth::user()->invoiceNumberFormat($note->invoice_id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Driver') }}</label>
                                <input type="text" name="driver_name" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Vehicle') }}</label>
                                <input type="text" name="vehicle_no" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Route date') }}</label>
                                <input type="date" name="route_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Route') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Retail Procurement Request') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.procurement.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Reference') }}</label>
                                <input type="text" name="reference" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Title') }}</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Store') }}</label>
                                <select name="retail_store_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Supplier') }}</label>
                                <select name="vender_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($venders as $vender)
                                        <option value="{{ $vender->id }}">{{ $vender->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Category') }}</label>
                                <select name="category_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Budget') }}</label>
                                <input type="number" step="0.01" name="budget_amount" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Needed by') }}</label>
                                <input type="date" name="needed_by" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="draft">{{ __('Draft') }}</option>
                                    <option value="pending">{{ __('Pending') }}</option>
                                    <option value="approved">{{ __('Approved') }}</option>
                                    <option value="ordered">{{ __('Ordered') }}</option>
                                    <option value="closed">{{ __('Closed') }}</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Procurement Request') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Store Replenishment') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('retail.operations.replenishments.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Source store') }}</label>
                                <select name="source_store_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Destination store') }}</label>
                                <select name="destination_store_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($retailStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Product') }}</label>
                                <select name="product_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Suggested qty') }}</label>
                                <input type="number" step="0.001" name="suggested_quantity" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Approved qty') }}</label>
                                <input type="number" step="0.001" name="approved_quantity" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Needed by') }}</label>
                                <input type="date" name="needed_by" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Replenishment') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Stores & Sessions') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Store') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Manager') }}</th>
                            <th>{{ __('Sessions') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($retailStores as $store)
                            <tr>
                                <td>{{ $store->name }}</td>
                                <td>{{ $store->code }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $store->store_type ?? 'store')) }}</td>
                                <td>{{ $store->manager_name ?: '-' }}</td>
                                <td>{{ $store->pos_sessions_count }}</td>
                                <td>{{ ucfirst($store->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('No stores yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('POS Sessions') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Store') }}</th>
                            <th>{{ __('Register') }}</th>
                            <th>{{ __('Opened') }}</th>
                            <th>{{ __('Transactions') }}</th>
                            <th>{{ __('Variance') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($posSessions as $session)
                            <tr>
                                <td>{{ optional($session->retailStore)->name ?: '-' }}</td>
                                <td>{{ optional($session->cashRegister)->name ?: '-' }}</td>
                                <td>{{ $session->opened_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $session->transactions_count }}</td>
                                <td>{{ \Auth::user()->priceFormat($session->variance_amount) }}</td>
                                <td>{{ ucfirst($session->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('No POS sessions yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Promotions') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Audience') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->name }}</td>
                                <td>{{ $promotion->code }}</td>
                                <td>{{ ucfirst($promotion->promotion_type) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $promotion->audience_type ?? 'all')) }}</td>
                                <td>{{ \Auth::user()->priceFormat($promotion->discount_value) }}</td>
                                <td>{{ ucfirst($promotion->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('No promotions yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Commercial Contracts') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Number') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Party') }}</th>
                            <th>{{ __('Store') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($commercialContracts as $contract)
                            <tr>
                                <td>{{ $contract->contract_number }}</td>
                                <td>{{ $contract->title }}</td>
                                <td>{{ $contract->party_name ?: ucfirst($contract->party_type) }}</td>
                                <td>{{ optional($contract->retailStore)->name ?: '-' }}</td>
                                <td>{{ \Auth::user()->priceFormat($contract->amount) }}</td>
                                <td>{{ ucfirst($contract->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">{{ __('No contracts yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Procurement Requests') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Store') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Budget') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($procurementRequests as $request)
                            <tr>
                                <td>{{ $request->reference }}</td>
                                <td>{{ optional($request->retailStore)->name ?: '-' }}</td>
                                <td>{{ optional($request->vender)->name ?: '-' }}</td>
                                <td>{{ \Auth::user()->priceFormat($request->budget_amount) }}</td>
                                <td>{{ ucfirst($request->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No procurement requests yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Replenishment Pipeline') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('From') }}</th>
                            <th>{{ __('To') }}</th>
                            <th>{{ __('Suggested') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($replenishments as $replenishment)
                            <tr>
                                <td>{{ optional($replenishment->product)->name ?: '-' }}</td>
                                <td>{{ optional($replenishment->sourceStore)->name ?: '-' }}</td>
                                <td>{{ optional($replenishment->destinationStore)->name ?: '-' }}</td>
                                <td>{{ $replenishment->suggested_quantity }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $replenishment->status)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No replenishment requests yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header"><h5>{{ __('Recent POS Sales') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('POS') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Warehouse') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Total') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($posSales as $sale)
                            <tr>
                                <td>{{ \Auth::user()->posNumberFormat($sale->pos_id) }}</td>
                                <td>{{ optional($sale->customer)->name ?: '-' }}</td>
                                <td>{{ optional($sale->warehouse)->name ?: '-' }}</td>
                                <td>{{ $sale->pos_date ? \Auth::user()->dateFormat($sale->pos_date) : '-' }}</td>
                                <td>{{ \Auth::user()->priceFormat($sale->getTotal()) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No POS sales found.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
