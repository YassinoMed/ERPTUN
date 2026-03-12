@extends('layouts.admin')

@section('page-title')
    {{ __('Property Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Properties') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create managed property')
            <a href="#" data-url="{{ route('managed-properties.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Property') }}" class="btn btn-sm btn-primary">
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
                            <th>{{ __('Property') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Manager') }}</th>
                            <th>{{ __('City') }}</th>
                            <th>{{ __('Units') }}</th>
                            <th>{{ __('Leases') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th width="220px">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($properties as $property)
                            <tr>
                                <td>
                                    <div>{{ $property->name }}</div>
                                    <small class="text-muted">{{ $property->property_code }}</small>
                                </td>
                                <td>{{ $property->property_type ?: '-' }}</td>
                                <td>{{ optional($property->manager)->name ?: '-' }}</td>
                                <td>{{ $property->city ?: '-' }}</td>
                                <td>{{ $property->units_count }}</td>
                                <td>{{ $property->leases_count }}</td>
                                <td>{{ __(ucfirst($property->status)) }}</td>
                                <td class="Action">
                                    <div class="action-btn me-2">
                                        <a href="{{ route('managed-properties.show', $property->id) }}" class="mx-3 btn btn-sm align-items-center bg-warning">
                                            <i class="ti ti-eye text-white"></i>
                                        </a>
                                    </div>
                                    @can('edit managed property')
                                        <div class="action-btn me-2">
                                            <a href="#" data-url="{{ route('managed-properties.edit', $property->id) }}" data-size="lg" data-ajax-popup="true"
                                                data-title="{{ __('Edit Property') }}" class="mx-3 btn btn-sm align-items-center bg-info">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                    @endcan
                                    @can('delete managed property')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['managed-properties.destroy', $property->id], 'id' => 'delete-property-' . $property->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-property-{{ $property->id }}').submit();">
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
