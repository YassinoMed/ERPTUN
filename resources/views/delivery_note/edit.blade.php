@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Delivery Note') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('delivery-note.index') }}">{{ __('Delivery Notes') }}</a></li>
    <li class="breadcrumb-item">{{ sprintf('DN-%05d', $deliveryNote->delivery_note_id) }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($deliveryNote, ['route' => ['delivery-note.update', $deliveryNote->id], 'method' => 'PUT']) !!}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Delivery Date') }}</label>
                                <input type="date" class="form-control" name="delivery_date" value="{{ $deliveryNote->delivery_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-control" required>
                                    @foreach ($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ $deliveryNote->status === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Reference') }}</label>
                                <input type="text" class="form-control" name="reference" value="{{ $deliveryNote->reference }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Tracking Number') }}</label>
                                <input type="text" class="form-control" name="tracking_number" value="{{ $deliveryNote->tracking_number }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Driver Name') }}</label>
                                <input type="text" class="form-control" name="driver_name" value="{{ $deliveryNote->driver_name }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Vehicle Number') }}</label>
                                <input type="text" class="form-control" name="vehicle_number" value="{{ $deliveryNote->vehicle_number }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Shipping Address') }}</label>
                                <input type="text" class="form-control" name="shipping_address" value="{{ $deliveryNote->shipping_address }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('Notes') }}</label>
                                <textarea name="notes" rows="2" class="form-control">{{ $deliveryNote->notes }}</textarea>
                            </div>
                        </div>
                    </div>

                    @php
                        $delivered = [];
                        foreach ($deliveryNote->invoice->deliveryNotes as $existingDelivery) {
                            if ($existingDelivery->id === $deliveryNote->id || $existingDelivery->status === 'cancelled') {
                                continue;
                            }
                            foreach ($existingDelivery->items as $existingItem) {
                                $delivered[$existingItem->invoice_product_id] = ($delivered[$existingItem->invoice_product_id] ?? 0) + (float) $existingItem->quantity;
                            }
                        }
                        $currentItems = $deliveryNote->items->keyBy('invoice_product_id');
                    @endphp

                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Invoiced Qty') }}</th>
                                    <th>{{ __('Already Delivered') }}</th>
                                    <th>{{ __('Remaining') }}</th>
                                    <th>{{ __('Deliver Qty') }}</th>
                                    <th>{{ __('Description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryNote->invoice->items as $invoiceItem)
                                    @php
                                        $alreadyDelivered = (float) ($delivered[$invoiceItem->id] ?? 0);
                                        $currentQuantity = (float) optional($currentItems->get($invoiceItem->id))->quantity;
                                        $remaining = max(0, (float) $invoiceItem->quantity - $alreadyDelivered);
                                    @endphp
                                    <tr>
                                        <td>{{ optional($invoiceItem->product)->name ?: __('Unknown product') }}</td>
                                        <td>{{ $invoiceItem->quantity }}</td>
                                        <td>{{ $alreadyDelivered }}</td>
                                        <td>{{ $remaining }}</td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="{{ $remaining }}" name="items[{{ $invoiceItem->id }}][quantity]" class="form-control" value="{{ $currentQuantity }}">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $invoiceItem->id }}][description]" class="form-control" value="{{ optional($currentItems->get($invoiceItem->id))->description ?: $invoiceItem->description }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 text-end">
                        <a href="{{ route('delivery-note.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
