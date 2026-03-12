@extends('layouts.admin')

@section('page-title')
    {{ __('Subsidiaries') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Subsidiaries') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create subsidiary')
            <a href="#" data-url="{{ route('subsidiaries.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Subsidiary') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Currency') }}</th>
                                    <th>{{ __('Ownership %') }}</th>
                                    <th>{{ __('Method') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subsidiaries as $subsidiary)
                                    <tr>
                                        <td>{{ $subsidiary->name }}</td>
                                        <td>{{ $subsidiary->country ?: '-' }}</td>
                                        <td>{{ $subsidiary->currency ?: '-' }}</td>
                                        <td>{{ $subsidiary->ownership_percentage }}</td>
                                        <td>{{ __(ucfirst($subsidiary->consolidation_method)) }}</td>
                                        <td>{{ __(ucfirst($subsidiary->status)) }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('subsidiaries.show', $subsidiary->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit subsidiary')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('subsidiaries/' . $subsidiary->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Subsidiary') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete subsidiary')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['subsidiaries.destroy', $subsidiary->id], 'id' => 'delete-form-' . $subsidiary->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $subsidiary->id }}').submit();">
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
        </div>
    </div>
@endsection
