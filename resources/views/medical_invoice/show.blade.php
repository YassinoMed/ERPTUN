@extends('layouts.admin')

@section('page-title'){{ $invoice->invoice_number }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical-invoices.index') }}">{{ __('Medical Billing') }}</a></li>
    <li class="breadcrumb-item">{{ $invoice->invoice_number }}</li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h5>{{ __('Invoice Summary') }}</h5>
            <div>{{ __('Patient') }}: {{ optional($invoice->patient)->first_name }} {{ optional($invoice->patient)->last_name }}</div>
            <div>{{ __('Status') }}: {{ ucfirst($invoice->status) }}</div>
            <div>{{ __('Total') }}: {{ \Auth::user()->priceFormat($invoice->total_amount) }}</div>
            <div>{{ __('Insurance') }}: {{ \Auth::user()->priceFormat($invoice->insurance_amount) }}</div>
            <div>{{ __('Patient Due') }}: {{ \Auth::user()->priceFormat($invoice->patient_amount) }}</div>
            <div>{{ __('Paid') }}: {{ \Auth::user()->priceFormat($invoice->paidAmount()) }}</div>
            <div>{{ __('Remaining') }}: {{ \Auth::user()->priceFormat($invoice->dueAmount()) }}</div>
        </div></div>
        @can('create medical invoice payment')
        <div class="card"><div class="card-body">
            <h5>{{ __('Add Payment') }}</h5>
            {{ Form::open(['route' => ['medical-invoice-payments.store', $invoice->id], 'method' => 'post']) }}
            {{ Form::label('payment_date', __('Payment Date'), ['class' => 'form-label']) }}{{ Form::date('payment_date', now()->format('Y-m-d'), ['class' => 'form-control']) }}
            {{ Form::label('amount', __('Amount'), ['class' => 'form-label mt-2']) }}{{ Form::number('amount', null, ['class' => 'form-control', 'step' => '0.01']) }}
            {{ Form::label('payment_method', __('Method'), ['class' => 'form-label mt-2']) }}{{ Form::text('payment_method', null, ['class' => 'form-control']) }}
            {{ Form::label('reference', __('Reference'), ['class' => 'form-label mt-2']) }}{{ Form::text('reference', null, ['class' => 'form-control']) }}
            <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
            {{ Form::close() }}
        </div></div>
        @endcan
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-body table-border-style"><div class="table-responsive">
            <table class="table"><thead><tr><th>{{ __('Description') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Unit Price') }}</th><th>{{ __('Coverage') }}</th><th>{{ __('Patient Due') }}</th></tr></thead><tbody>@foreach($invoice->items as $item)<tr><td>{{ $item->description }}</td><td>{{ $item->quantity }}</td><td>{{ \Auth::user()->priceFormat($item->unit_price) }}</td><td>{{ $item->coverage_rate }}%</td><td>{{ \Auth::user()->priceFormat($item->patient_amount) }}</td></tr>@endforeach</tbody></table>
        </div></div></div>
        <div class="card"><div class="card-body table-border-style"><div class="table-responsive">
            <table class="table"><thead><tr><th>{{ __('Date') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Method') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@forelse($invoice->payments as $payment)<tr><td>{{ \Auth::user()->dateFormat($payment->payment_date) }}</td><td>{{ \Auth::user()->priceFormat($payment->amount) }}</td><td>{{ $payment->payment_method ?? '-' }}</td><td>{{ $payment->reference ?? '-' }}</td><td>@can('delete medical invoice payment'){!! Form::open(['method'=>'DELETE','route'=>['medical-invoice-payments.destroy',$payment->id],'id'=>'delete-medpay-'.$payment->id,'class'=>'d-inline']) !!}<a href="#" class="btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-medpay-{{ $payment->id }}').submit();"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}@endcan</td></tr>@empty<tr><td colspan="5" class="text-center">{{ __('No payments available') }}</td></tr>@endforelse</tbody></table>
        </div></div></div>
    </div>
</div>
@endsection
