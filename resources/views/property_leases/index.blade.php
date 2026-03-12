@extends('layouts.admin')

@section('page-title')
    {{ __('Property Leases') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Property Leases') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create property lease')
            <a href="#" data-url="{{ route('property-leases.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Property Lease') }}" class="btn btn-sm btn-primary">
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
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Property') }}</th>
                            <th>{{ __('Unit') }}</th>
                            <th>{{ __('Tenant') }}</th>
                            <th>{{ __('Period') }}</th>
                            <th>{{ __('Rent') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th width="220px">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leases as $lease)
                            <tr>
                                <td>{{ $lease->reference }}</td>
                                <td>{{ optional($lease->property)->name ?: '-' }}</td>
                                <td>{{ optional($lease->unit)->unit_code ?: '-' }}</td>
                                <td>{{ optional($lease->customer)->name ?: '-' }}</td>
                                <td>{{ Auth::user()->dateFormat($lease->start_date) }} - {{ $lease->end_date ? Auth::user()->dateFormat($lease->end_date) : '-' }}</td>
                                <td>{{ Auth::user()->priceFormat($lease->rent_amount) }}</td>
                                <td>{{ __(ucfirst(str_replace('_', ' ', $lease->status))) }}</td>
                                <td class="Action">
                                    <div class="action-btn me-2">
                                        <a href="{{ route('property-leases.show', $lease->id) }}" class="mx-3 btn btn-sm align-items-center bg-warning">
                                            <i class="ti ti-eye text-white"></i>
                                        </a>
                                    </div>
                                    @can('edit property lease')
                                        <div class="action-btn me-2">
                                            <a href="#" data-url="{{ route('property-leases.edit', $lease->id) }}" data-size="lg" data-ajax-popup="true"
                                                data-title="{{ __('Edit Property Lease') }}" class="mx-3 btn btn-sm align-items-center bg-info">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                    @endcan
                                    @can('delete property lease')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['property-leases.destroy', $lease->id], 'id' => 'delete-property-lease-' . $lease->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-property-lease-{{ $lease->id }}').submit();">
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
