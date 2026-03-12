@extends('layouts.admin')

@section('page-title')
    {{ __('Visitors') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Visitors') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create visitor')
            <a href="#" data-url="{{ route('visitors.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Register Visitor') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Visitor') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Host') }}</th>
                                    <th>{{ __('Visit Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Badge') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visitors as $visitor)
                                    <tr>
                                        <td>{{ $visitor->visitor_name }}</td>
                                        <td>{{ $visitor->company_name ?: '-' }}</td>
                                        <td>{{ optional($visitor->host)->name ?: '-' }}</td>
                                        <td>{{ Auth::user()->dateFormat($visitor->visit_date) }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $visitor->status))) }}</td>
                                        <td>{{ $visitor->badge_number ?: '-' }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('visitors.show', $visitor->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit visitor')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('visitors/' . $visitor->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Visitor') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete visitor')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['visitors.destroy', $visitor->id], 'id' => 'delete-form-' . $visitor->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $visitor->id }}').submit();">
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
