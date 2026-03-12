@extends('layouts.admin')

@section('page-title')
    {{ __('Medical Services') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Medical Services') }}</li>
@endsection
@section('action-btn')
    <div class="float-end d-flex">
        @can('create medical service')
            <a href="#" data-size="lg" data-url="{{ route('medical-services.create') }}" data-ajax-popup="true" data-title="{{ __('Create Medical Service') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <div class="card"><div class="card-body table-border-style"><div class="table-responsive">
        <table class="table datatable">
            <thead><tr><th>{{ __('Code') }}</th><th>{{ __('Name') }}</th><th>{{ __('Type') }}</th><th>{{ __('Price') }}</th><th>{{ __('Coverage') }}</th><th>{{ __('Action') }}</th></tr></thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td>{{ $service->code ?? '-' }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->service_type ?? '-' }}</td>
                    <td>{{ \Auth::user()->priceFormat($service->price) }}</td>
                    <td>{{ $service->default_coverage_rate }}%</td>
                    <td>
                        @can('edit medical service')
                            <a href="#" class="btn btn-sm bg-info" data-url="{{ route('medical-services.edit', $service->id) }}" data-ajax-popup="true" data-title="{{ __('Edit Medical Service') }}"><i class="ti ti-pencil text-white"></i></a>
                        @endcan
                        @can('delete medical service')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['medical-services.destroy', $service->id], 'id' => 'delete-service-' . $service->id, 'class' => 'd-inline']) !!}
                            <a href="#" class="btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-service-{{ $service->id }}').submit();"><i class="ti ti-trash text-white"></i></a>
                            {!! Form::close() !!}
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div></div>
@endsection
