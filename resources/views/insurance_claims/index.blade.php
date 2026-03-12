@extends('layouts.admin')

@section('page-title')
    {{ __('Insurance Claims') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Insurance Claims') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create insurance claim')
            <a href="#" data-url="{{ route('insurance-claims.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Insurance Claim') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Claim') }}</th>
                                    <th>{{ __('Policy') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Incident Date') }}</th>
                                    <th>{{ __('Amount Claimed') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($claims as $claim)
                                    <tr>
                                        <td>{{ $claim->claim_number }}</td>
                                        <td>{{ optional($claim->policy)->policy_name ?: '-' }}</td>
                                        <td>{{ optional($claim->customer)->name ?: '-' }}</td>
                                        <td>{{ $claim->incident_date ? Auth::user()->dateFormat($claim->incident_date) : '-' }}</td>
                                        <td>{{ Auth::user()->priceFormat($claim->amount_claimed) }}</td>
                                        <td>{{ __(ucfirst($claim->priority)) }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $claim->status))) }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('insurance-claims.show', $claim->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit insurance claim')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ route('insurance-claims.edit', $claim->id) }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Insurance Claim') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete insurance claim')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['insurance-claims.destroy', $claim->id], 'id' => 'delete-form-claim-' . $claim->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-claim-{{ $claim->id }}').submit();">
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
