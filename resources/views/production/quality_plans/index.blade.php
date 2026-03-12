@extends('layouts.admin')
@section('page-title'){{ __('Quality Plans') }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Quality Plans') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">@can('create industrial quality plan')<a href="#" data-size="lg" data-url="{{ route('production.quality-plans.create') }}" data-ajax-popup="true" data-title="{{ __('Create Quality Plan') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endcan</div>
@endsection
@section('content')
    <div class="card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table datatable">
        <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Product') }}</th><th>{{ __('Routing') }}</th><th>{{ __('Stage') }}</th><th>{{ __('Status') }}</th><th>{{ __('Action') }}</th></tr></thead>
        <tbody>@foreach($qualityPlans as $qualityPlan)<tr><td>{{ $qualityPlan->name }}</td><td>{{ $qualityPlan->product?->name ?: '-' }}</td><td>{{ $qualityPlan->routing?->name ?: '-' }}</td><td>{{ ucfirst(str_replace('_',' ', $qualityPlan->check_stage)) }}</td><td>{{ ucfirst($qualityPlan->status) }}</td><td class="Action">@can('edit industrial quality plan')<div class="action-btn me-2"><a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('production.quality-plans.edit', $qualityPlan->id) }}" data-ajax-popup="true" data-size="lg"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete industrial quality plan')<div class="action-btn">{!! Form::open(['method'=>'DELETE','route'=>['production.quality-plans.destroy',$qualityPlan->id],'id'=>'delete-form-quality-'.$qualityPlan->id]) !!}<a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}</div>@endcan</td></tr>@endforeach</tbody>
    </table></div></div></div>
@endsection
