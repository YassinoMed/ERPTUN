@extends('layouts.admin')

@section('page-title')
    {{ __('Customer Recoveries') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Customer Recoveries') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Monitor overdue exposure, escalation pressure and collection workload in one queue.') }}
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create customer recovery')
            <a href="#" data-url="{{ route('customer-recoveries.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Recovery Case') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @php
        $recoveryCollection = collect($recoveries);
        $openRecoveries = $recoveryCollection->filter(function ($recovery) {
            return !in_array($recovery->status, ['closed', 'resolved', 'paid'], true);
        })->count();
        $priorityRecoveries = $recoveryCollection->where('priority', 'high')->count() + $recoveryCollection->where('priority', 'critical')->count();
        $totalRecoveryDue = $recoveryCollection->sum('due_amount');
        $escalatedRecoveries = $recoveryCollection->where('stage', 'legal')->count() + $recoveryCollection->where('stage', 'escalated')->count();
    @endphp

    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Open recovery cases') }}</span>
            <strong class="ux-kpi-value">{{ $openRecoveries }}</strong>
            <span class="ux-kpi-meta">{{ __('Cases still requiring action') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('High priority cases') }}</span>
            <strong class="ux-kpi-value">{{ $priorityRecoveries }}</strong>
            <span class="ux-kpi-meta">{{ __('Accounts with immediate follow-up pressure') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Outstanding amount') }}</span>
            <strong class="ux-kpi-value">{{ Auth::user()->priceFormat($totalRecoveryDue) }}</strong>
            <span class="ux-kpi-meta">{{ __('Overdue exposure managed by the team') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Escalated files') }}</span>
            <strong class="ux-kpi-value">{{ $escalatedRecoveries }}</strong>
            <span class="ux-kpi-meta">{{ __('Cases already pushed to advanced treatment') }}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Reference') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Stage') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Due Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recoveries as $recovery)
                                    <tr data-bulk-id="{{ $recovery->id }}">
                                        <td>{{ $recovery->reference ?: ('REC-' . $recovery->id) }}</td>
                                        <td>{{ optional($recovery->customer)->name ?: '-' }}</td>
                                        <td>{{ optional($recovery->invoice)->invoice_id ?: '-' }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $recovery->stage))) }}</td>
                                        <td>{{ __(ucfirst($recovery->priority)) }}</td>
                                        <td>{{ Auth::user()->priceFormat($recovery->due_amount) }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $recovery->status))) }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('customer-recoveries.show', $recovery->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit customer recovery')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('customer-recoveries/' . $recovery->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Recovery Case') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete customer recovery')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['customer-recoveries.destroy', $recovery->id], 'id' => 'delete-form-' . $recovery->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $recovery->id }}').submit();">
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
