@extends('layouts.admin')

@section('page-title')
    {{ __('Innovation Ideas') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Innovation Ideas') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create innovation idea')
            <a href="#" data-url="{{ route('innovation-ideas.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Innovation Idea') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Submitter') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Expected Value') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ideas as $idea)
                                    <tr>
                                        <td>{{ $idea->title }}</td>
                                        <td>{{ $idea->category ?: '-' }}</td>
                                        <td>{{ optional($idea->submitter)->name ?: '-' }}</td>
                                        <td>{{ __(ucfirst($idea->priority)) }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $idea->status))) }}</td>
                                        <td>{{ Auth::user()->priceFormat($idea->expected_value) }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('innovation-ideas.show', $idea->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit innovation idea')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('innovation-ideas/' . $idea->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Innovation Idea') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete innovation idea')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['innovation-ideas.destroy', $idea->id], 'id' => 'delete-form-' . $idea->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $idea->id }}').submit();">
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
