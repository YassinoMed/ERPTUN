@extends('layouts.admin')
@section('page-title')
    {{ __('Create Delivery Note') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('delivery-note.index') }}">{{ __('Delivery Notes') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection
@push('script-page')
    <script>
        function reloadDeliveryInvoice(select) {
            const value = select.value;
            const target = "{{ route('delivery-note.create') }}";
            window.location = value ? `${target}?invoice_id=${value}` : target;
        }
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Invoice') }}</label>
                            <select class="form-control" onchange="reloadDeliveryInvoice(this)">
                                <option value="">{{ __('Select Invoice') }}</option>
                                @foreach ($invoices as $invoice)
                                    <option value="{{ $invoice->id }}" {{ optional($selectedInvoice)->id === $invoice->id ? 'selected' : '' }}>
                                        {{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }} - {{ optional($invoice->customer)->name ?: __('Unknown customer') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($selectedInvoice)
                        {!! Form::open(['route' => 'delivery-note.store', 'method' => 'post']) !!}
                        <input type="hidden" name="invoice_id" value="{{ $selectedInvoice->id }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Delivery Date') }}</label>
                                    <input type="date" class="form-control" name="delivery_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <select name="status" class="form-control" required>
                                        @foreach ($statuses as $key => $label)
                                            <option value="{{ $key }}">{{ __($label) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Reference') }}</label>
                                    <input type="text" class="form-control" name="reference">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Tracking Number') }}</label>
                                    <input type="text" class="form-control" name="tracking_number">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Driver Name') }}</label>
                                    <input type="text" class="form-control" name="driver_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Vehicle Number') }}</label>
                                    <input type="text" class="form-control" name="vehicle_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Shipping Address') }}</label>
                                    <input type="text" class="form-control" name="shipping_address" value="{{ optional($selectedInvoice->customer)->shipping_address }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Notes') }}</label>
                                    <textarea name="notes" rows="2" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        @php
                            $delivered = [];
                            foreach ($selectedInvoice->deliveryNotes as $existingDelivery) {
                                if ($existingDelivery->status === 'cancelled') {
                                    continue;
                                }
                                foreach ($existingDelivery->items as $existingItem) {
                                    $delivered[$existingItem->invoice_product_id] = ($delivered[$existingItem->invoice_product_id] ?? 0) + (float) $existingItem->quantity;
                                }
                            }
                        @endphp

                        <div class="table-responsive mt-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Invoiced Qty') }}</th>
                                        <th>{{ __('Already Delivered') }}</th>
                                        <th>{{ __('Remaining') }}</th>
                                        <th>{{ __('Deliver Now') }}</th>
                                        <th>{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedInvoice->items as $invoiceItem)
                                        @php
                                            $alreadyDelivered = (float) ($delivered[$invoiceItem->id] ?? 0);
                                            $remaining = max(0, (float) $invoiceItem->quantity - $alreadyDelivered);
                                        @endphp
                                        <tr>
                                            <td>{{ optional($invoiceItem->product)->name ?: __('Unknown product') }}</td>
                                            <td>{{ $invoiceItem->quantity }}</td>
                                            <td>{{ $alreadyDelivered }}</td>
                                            <td>{{ $remaining }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="{{ $remaining }}" name="items[{{ $invoiceItem->id }}][quantity]" class="form-control" value="{{ $remaining }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $invoiceItem->id }}][description]" class="form-control" value="{{ $invoiceItem->description }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 text-end">
                            <a href="{{ route('delivery-note.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                        </div>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
