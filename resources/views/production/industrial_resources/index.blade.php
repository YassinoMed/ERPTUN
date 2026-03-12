@extends('layouts.admin')
@section('page-title')
    {{ __('Industrial Resources') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Industrial Resources') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create industrial resource')
            <a href="#" data-size="lg" data-url="{{ route('production.resources.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create Industrial Resource') }}"
                class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Parent') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Capacity') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resources as $resource)
                                    <tr>
                                        <td>{{ $resource->code ?: '-' }}</td>
                                        <td>
                                            @can('show industrial resource')
                                                <a href="{{ route('production.resources.show', $resource->id) }}">{{ $resource->name }}</a>
                                            @else
                                                {{ $resource->name }}
                                            @endcan
                                        </td>
                                        <td>{{ ucfirst($resource->type) }}</td>
                                        <td>{{ $resource->parent?->name ?: '-' }}</td>
                                        <td>{{ ucfirst($resource->status) }}</td>
                                        <td>{{ $resource->capacity_hours_per_day }}h / {{ $resource->capacity_workers }} {{ __('workers') }}</td>
                                        <td class="Action">
                                            @can('edit industrial resource')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                        data-url="{{ route('production.resources.edit', $resource->id) }}"
                                                        data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                        title="{{ __('Edit') }}" data-title="{{ __('Edit Industrial Resource') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete industrial resource')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['production.resources.destroy', $resource->id], 'id' => 'delete-form-resource-' . $resource->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
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
