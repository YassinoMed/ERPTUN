@extends('layouts.admin')
@section('page-title')
    {{ __('Routings') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Routings') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create production routing')
            <a href="#" data-size="xl" data-url="{{ route('production.routings.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create Routing') }}"
                class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>
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
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Steps') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($routings as $routing)
                                    <tr>
                                        <td>{{ $routing->code ?: '-' }}</td>
                                        <td><a href="{{ route('production.routings.show', $routing->id) }}">{{ $routing->name }}</a></td>
                                        <td>{{ $routing->product?->name ?: '-' }}</td>
                                        <td>{{ $routing->steps->count() }}</td>
                                        <td>{{ ucfirst($routing->status) }}</td>
                                        <td class="Action">
                                            @can('edit production routing')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                        data-url="{{ route('production.routings.edit', $routing->id) }}"
                                                        data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                        title="{{ __('Edit') }}" data-title="{{ __('Edit Routing') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete production routing')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['production.routings.destroy', $routing->id], 'id' => 'delete-form-routing-' . $routing->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
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
