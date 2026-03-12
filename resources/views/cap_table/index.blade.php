@extends('layouts.admin')

@section('page-title')
    {{ __('Cap Table') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Cap Table') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create cap table')
            <a href="#" data-url="{{ route('cap-table.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Cap Table Entry') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Holder') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Class') }}</th>
                                    <th>{{ __('Shares') }}</th>
                                    <th>{{ __('Ownership %') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->holder_name }}</td>
                                        <td>{{ __(ucfirst($entry->holder_type)) }}</td>
                                        <td>{{ $entry->share_class ?: '-' }}</td>
                                        <td>{{ $entry->share_count }}</td>
                                        <td>{{ $entry->ownership_percentage }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('cap-table.show', $entry->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning"
                                                    data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit cap table')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('cap-table/' . $entry->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Cap Table Entry') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete cap table')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['cap-table.destroy', $entry->id], 'id' => 'delete-form-' . $entry->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $entry->id }}').submit();">
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
