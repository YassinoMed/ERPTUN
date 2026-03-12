@extends('layouts.admin')

@section('page-title')
    {{ __('Property Units') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Property Units') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create property unit')
            <a href="#" data-url="{{ route('property-units.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Property Unit') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Unit') }}</th>
                            <th>{{ __('Property') }}</th>
                            <th>{{ __('Floor') }}</th>
                            <th>{{ __('Area') }}</th>
                            <th>{{ __('Monthly Rent') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th width="220px">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $unit)
                            <tr>
                                <td>{{ $unit->unit_code }}</td>
                                <td>{{ optional($unit->property)->name ?: '-' }}</td>
                                <td>{{ $unit->floor ?: '-' }}</td>
                                <td>{{ $unit->area }}</td>
                                <td>{{ Auth::user()->priceFormat($unit->monthly_rent) }}</td>
                                <td>{{ __(ucfirst($unit->status)) }}</td>
                                <td class="Action">
                                    <div class="action-btn me-2">
                                        <a href="{{ route('property-units.show', $unit->id) }}" class="mx-3 btn btn-sm align-items-center bg-warning">
                                            <i class="ti ti-eye text-white"></i>
                                        </a>
                                    </div>
                                    @can('edit property unit')
                                        <div class="action-btn me-2">
                                            <a href="#" data-url="{{ route('property-units.edit', $unit->id) }}" data-size="lg" data-ajax-popup="true"
                                                data-title="{{ __('Edit Property Unit') }}" class="mx-3 btn btn-sm align-items-center bg-info">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                    @endcan
                                    @can('delete property unit')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['property-units.destroy', $unit->id], 'id' => 'delete-property-unit-' . $unit->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-property-unit-{{ $unit->id }}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
