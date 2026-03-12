@extends('layouts.admin')
@section('page-title'){{ __('Subcontract Orders') }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Subcontract Orders') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('create industrial subcontract order')
            <a href="#" data-size="lg" data-url="{{ route('production.subcontract-orders.create') }}" data-ajax-popup="true" data-title="{{ __('Create Subcontract Order') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table datatable">
        <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Order') }}</th><th>{{ __('Vendor') }}</th><th>{{ __('Quantity') }}</th><th>{{ __('Status') }}</th><th>{{ __('Action') }}</th></tr></thead>
        <tbody>
        @foreach($subcontractOrders as $subcontractOrder)
            <tr>
                <td><a href="{{ route('production.subcontract-orders.show', $subcontractOrder->id) }}">{{ $subcontractOrder->reference ?: ('SUB-'.$subcontractOrder->id) }}</a></td>
                <td>{{ $subcontractOrder->order?->order_number ?: '-' }}</td>
                <td>{{ $subcontractOrder->vendor?->name ?: '-' }}</td>
                <td>{{ $subcontractOrder->quantity }}</td>
                <td>{{ ucfirst(str_replace('_',' ', $subcontractOrder->status)) }}</td>
                <td class="Action">
                    @can('edit industrial subcontract order')
                        <div class="action-btn me-2"><a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('production.subcontract-orders.edit', $subcontractOrder->id) }}" data-ajax-popup="true" data-size="lg" title="{{ __('Edit') }}"><i class="ti ti-pencil text-white"></i></a></div>
                    @endcan
                    @can('delete industrial subcontract order')
                        <div class="action-btn">{!! Form::open(['method'=>'DELETE','route'=>['production.subcontract-orders.destroy',$subcontractOrder->id],'id'=>'delete-form-sub-'.$subcontractOrder->id]) !!}<a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" title="{{ __('Delete') }}"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}</div>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div></div>
@endsection
