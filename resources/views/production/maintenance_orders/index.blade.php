@extends('layouts.admin')
@section('page-title'){{ __('Maintenance Orders') }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Maintenance Orders') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">@can('create industrial maintenance order')<a href="#" data-size="lg" data-url="{{ route('production.maintenance-orders.create') }}" data-ajax-popup="true" data-title="{{ __('Create Maintenance Order') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endcan</div>
@endsection
@section('content')
    <div class="card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table datatable">
        <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Work Center') }}</th><th>{{ __('Type') }}</th><th>{{ __('Status') }}</th><th>{{ __('Downtime') }}</th><th>{{ __('Action') }}</th></tr></thead>
        <tbody>@foreach($maintenanceOrders as $maintenanceOrder)<tr><td>{{ $maintenanceOrder->reference ?: ('MO-'.$maintenanceOrder->id) }}</td><td>{{ $maintenanceOrder->workCenter?->name ?: '-' }}</td><td>{{ ucfirst($maintenanceOrder->type) }}</td><td>{{ ucfirst(str_replace('_',' ', $maintenanceOrder->status)) }}</td><td>{{ $maintenanceOrder->downtime_minutes }} {{ __('min') }}</td><td class="Action">@can('edit industrial maintenance order')<div class="action-btn me-2"><a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('production.maintenance-orders.edit', $maintenanceOrder->id) }}" data-ajax-popup="true" data-size="lg"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete industrial maintenance order')<div class="action-btn">{!! Form::open(['method'=>'DELETE','route'=>['production.maintenance-orders.destroy',$maintenanceOrder->id],'id'=>'delete-form-maint-'.$maintenanceOrder->id]) !!}<a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}</div>@endcan</td></tr>@endforeach</tbody>
    </table></div></div></div>
@endsection
