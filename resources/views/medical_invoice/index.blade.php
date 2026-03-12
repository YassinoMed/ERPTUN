@extends('layouts.admin')

@section('page-title'){{ __('Medical Billing') }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Medical Billing') }}</li>
@endsection
@section('action-btn')
    <div class="float-end d-flex">@can('create medical invoice')<a href="#" data-size="xl" data-url="{{ route('medical-invoices.create') }}" data-ajax-popup="true" data-title="{{ __('Create Medical Invoice') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endcan</div>
@endsection
@section('content')
<div class="card"><div class="card-body table-border-style"><div class="table-responsive">
    <table class="table datatable">
        <thead><tr><th>{{ __('Invoice') }}</th><th>{{ __('Patient') }}</th><th>{{ __('Date') }}</th><th>{{ __('Total') }}</th><th>{{ __('Patient Due') }}</th><th>{{ __('Status') }}</th><th>{{ __('Action') }}</th></tr></thead>
        <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td><a href="{{ route('medical-invoices.show', $invoice->id) }}" class="text-primary">{{ $invoice->invoice_number }}</a></td>
                <td>{{ optional($invoice->patient)->first_name }} {{ optional($invoice->patient)->last_name }}</td>
                <td>{{ \Auth::user()->dateFormat($invoice->invoice_date) }}</td>
                <td>{{ \Auth::user()->priceFormat($invoice->total_amount) }}</td>
                <td>{{ \Auth::user()->priceFormat($invoice->patient_amount) }}</td>
                <td>{{ ucfirst($invoice->status) }}</td>
                <td>
                    @can('show medical invoice')<a href="{{ route('medical-invoices.show', $invoice->id) }}" class="btn btn-sm bg-warning"><i class="ti ti-eye text-white"></i></a>@endcan
                    @can('edit medical invoice')<a href="#" class="btn btn-sm bg-info" data-url="{{ route('medical-invoices.edit', $invoice->id) }}" data-ajax-popup="true" data-size="xl" data-title="{{ __('Edit Medical Invoice') }}"><i class="ti ti-pencil text-white"></i></a>@endcan
                    @can('delete medical invoice'){!! Form::open(['method'=>'DELETE','route'=>['medical-invoices.destroy',$invoice->id],'id'=>'delete-minvoice-'.$invoice->id,'class'=>'d-inline']) !!}<a href="#" class="btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-minvoice-{{ $invoice->id }}').submit();"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}@endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div></div>
@endsection
