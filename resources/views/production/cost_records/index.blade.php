@extends('layouts.admin')
@section('page-title'){{ __('Industrial Cost Records') }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Industrial Cost Records') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">@can('create industrial cost record')<a href="#" data-size="lg" data-url="{{ route('production.cost-records.create') }}" data-ajax-popup="true" data-title="{{ __('Create Cost Record') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endcan</div>
@endsection
@section('content')
    <div class="card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table datatable">
        <thead><tr><th>{{ __('Order') }}</th><th>{{ __('Product') }}</th><th>{{ __('Type') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Action') }}</th></tr></thead>
        <tbody>@foreach($costRecords as $costRecord)<tr><td>{{ $costRecord->order?->order_number ?: '-' }}</td><td>{{ $costRecord->product?->name ?: ($costRecord->order?->product?->name ?: '-') }}</td><td>{{ ucfirst($costRecord->cost_type) }}</td><td>{{ \Auth::user()->priceFormat($costRecord->amount) }}</td><td class="Action">@can('edit industrial cost record')<div class="action-btn me-2"><a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('production.cost-records.edit', $costRecord->id) }}" data-ajax-popup="true" data-size="lg"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete industrial cost record')<div class="action-btn">{!! Form::open(['method'=>'DELETE','route'=>['production.cost-records.destroy',$costRecord->id],'id'=>'delete-form-cost-'.$costRecord->id]) !!}<a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}</div>@endcan</td></tr>@endforeach</tbody>
    </table></div></div></div>
@endsection
