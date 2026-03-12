@extends('layouts.admin')

@section('page-title')
    {{ __('Insurance Policies') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Insurance Policies') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create insurance policy')
            <a href="#" data-url="{{ route('insurance-policies.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Insurance Policy') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Policy') }}</th>
                                    <th>{{ __('Provider') }}</th>
                                    <th>{{ __('Coverage') }}</th>
                                    <th>{{ __('Period') }}</th>
                                    <th>{{ __('Claims') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($policies as $policy)
                                    <tr>
                                        <td>
                                            <div>{{ $policy->policy_name }}</div>
                                            <small class="text-muted">{{ $policy->policy_number }}</small>
                                        </td>
                                        <td>{{ $policy->provider_name }}</td>
                                        <td>{{ $policy->coverage_type ?: '-' }}</td>
                                        <td>
                                            {{ $policy->start_date ? Auth::user()->dateFormat($policy->start_date) : '-' }}
                                            <br>
                                            <small class="text-muted">{{ $policy->end_date ? Auth::user()->dateFormat($policy->end_date) : '-' }}</small>
                                        </td>
                                        <td>{{ $policy->claims_count }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $policy->status))) }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('insurance-policies.show', $policy->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit insurance policy')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ route('insurance-policies.edit', $policy->id) }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Insurance Policy') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete insurance policy')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['insurance-policies.destroy', $policy->id], 'id' => 'delete-form-policy-' . $policy->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-policy-{{ $policy->id }}').submit();">
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
