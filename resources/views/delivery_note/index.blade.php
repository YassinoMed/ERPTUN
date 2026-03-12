@extends('layouts.admin')
@section('page-title')
    {{ __('Delivery Notes') }}
@endsection
@section('page-subtitle')
    {{ __('Monitor shipping progress, partial deliveries and field execution from the sales flow.') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Delivery Notes') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('create delivery note')
            <a href="{{ route('delivery-note.create') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    @php
        $statusCounts = $deliveryNotes->groupBy('status')->map->count();
        $totalQuantity = $deliveryNotes->sum(function ($deliveryNote) {
            return $deliveryNote->getTotalQuantity();
        });
    @endphp
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Total delivery notes') }}</span>
            <strong class="ux-kpi-value">{{ $deliveryNotes->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('sales logistics records') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Dispatched') }}</span>
            <strong class="ux-kpi-value">{{ $statusCounts['dispatched'] ?? 0 }}</strong>
            <span class="ux-kpi-meta">{{ __('in transit') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Delivered') }}</span>
            <strong class="ux-kpi-value">{{ $statusCounts['delivered'] ?? 0 }}</strong>
            <span class="ux-kpi-meta">{{ __('completed notes') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Quantity moved') }}</span>
            <strong class="ux-kpi-value">{{ $totalQuantity }}</strong>
            <span class="ux-kpi-meta">{{ __('units delivered') }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Delivery Note') }}</th>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Delivery Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryNotes as $deliveryNote)
                                    <tr data-bulk-id="{{ $deliveryNote->id }}">
                                        <td>
                                            <a href="{{ route('delivery-note.show', $deliveryNote->id) }}" class="btn btn-outline-primary">
                                                {{ sprintf('DN-%05d', $deliveryNote->delivery_note_id) }}
                                            </a>
                                        </td>
                                        <td>{{ optional($deliveryNote->invoice)->invoice_id ? \Auth::user()->invoiceNumberFormat($deliveryNote->invoice->invoice_id) : '-' }}</td>
                                        <td>{{ optional($deliveryNote->customer)->name ?: '-' }}</td>
                                        <td>{{ \Auth::user()->dateFormat($deliveryNote->delivery_date) }}</td>
                                        <td>
                                            <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\DeliveryNote::$statuses[$deliveryNote->status] ?? ucfirst($deliveryNote->status)) }}</span>
                                        </td>
                                        <td>{{ $deliveryNote->getTotalQuantity() }}</td>
                                        <td class="Action">
                                            <span class="d-flex">
                                                @can('show delivery note')
                                                    <div class="action-btn me-2">
                                                        <a href="{{ route('delivery-note.show', $deliveryNote->id) }}" class="btn btn-sm bg-warning">
                                                            <i class="ti ti-eye text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('edit delivery note')
                                                    <div class="action-btn me-2">
                                                        <a href="{{ route('delivery-note.edit', $deliveryNote->id) }}" class="btn btn-sm bg-info">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete delivery note')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['delivery-note.destroy', $deliveryNote->id], 'id' => 'delete-form-' . $deliveryNote->id]) !!}
                                                        <a href="#" class="btn btn-sm bg-danger bs-pass-para">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
