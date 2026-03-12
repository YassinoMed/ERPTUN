@extends('layouts.admin')
@section('page-title')
    {{ __('Delivery Note Detail') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('delivery-note.index') }}">{{ __('Delivery Notes') }}</a></li>
    <li class="breadcrumb-item">{{ sprintf('DN-%05d', $deliveryNote->delivery_note_id) }}</li>
@endsection
@section('action-btn')
    <div class="float-end d-flex">
        @can('edit delivery note')
            <a href="{{ route('delivery-note.edit', $deliveryNote->id) }}" class="btn btn-sm btn-info me-2">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ sprintf('DN-%05d', $deliveryNote->delivery_note_id) }}</h5>
                    <p class="mb-1">{{ __('Status') }}: <strong>{{ __(\App\Models\DeliveryNote::$statuses[$deliveryNote->status] ?? ucfirst($deliveryNote->status)) }}</strong></p>
                    <p class="mb-1">{{ __('Delivery Date') }}: {{ \Auth::user()->dateFormat($deliveryNote->delivery_date) }}</p>
                    <p class="mb-1">{{ __('Invoice') }}: {{ optional($deliveryNote->invoice)->invoice_id ? \Auth::user()->invoiceNumberFormat($deliveryNote->invoice->invoice_id) : '-' }}</p>
                    <p class="mb-1">{{ __('Customer') }}: {{ optional($deliveryNote->customer)->name ?: '-' }}</p>
                    <p class="mb-1">{{ __('Driver') }}: {{ $deliveryNote->driver_name ?: '-' }}</p>
                    <p class="mb-1">{{ __('Vehicle') }}: {{ $deliveryNote->vehicle_number ?: '-' }}</p>
                    <p class="mb-1">{{ __('Tracking') }}: {{ $deliveryNote->tracking_number ?: '-' }}</p>
                    <p class="mb-0">{{ __('Shipping Address') }}: {{ $deliveryNote->shipping_address ?: '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryNote->items as $item)
                                    <tr>
                                        <td>{{ optional($item->product)->name ?: __('Unknown product') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->description ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($deliveryNote->notes)
                        <div class="mt-3">
                            <h6>{{ __('Notes') }}</h6>
                            <p class="mb-0">{{ $deliveryNote->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
