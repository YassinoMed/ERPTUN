@extends('layouts.admin')

@section('page-title')
    {{ __('Internal ITSM') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Internal ITSM') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create internal itsm')
            <a href="#" data-url="{{ route('internal-itsm.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create ITSM Ticket') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row mb-4 gy-3">
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">{{ __('Total') }}</div><h4>{{ $stats['total'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">{{ __('Open') }}</div><h4>{{ $stats['open'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">{{ __('On Hold') }}</div><h4>{{ $stats['on_hold'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">{{ __('Closed') }}</div><h4>{{ $stats['close'] }}</h4></div></div></div>
    </div>
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                    <tr>
                        <th>{{ __('Ticket') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Assignee') }}</th>
                        <th>{{ __('CI') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('SLA Due') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th width="220px">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td><div>{{ $ticket->subject }}</div><small class="text-muted">{{ $ticket->ticket_code }}</small></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $ticket->ticket_type ?: '-')) }}</td>
                            <td>{{ optional($ticket->assignUser)->name ?: '-' }}</td>
                            <td>{{ optional($ticket->configurationItem)->name ?: '-' }}</td>
                            <td>{{ $ticket->priority }}</td>
                            <td>{{ $ticket->resolution_due_at ? Auth::user()->dateFormat($ticket->resolution_due_at) : '-' }}</td>
                            <td>{{ $ticket->status }}</td>
                            <td class="Action">
                                <div class="action-btn me-2">
                                    <a href="{{ route('internal-itsm.show', $ticket->id) }}" class="mx-3 btn btn-sm align-items-center bg-warning">
                                        <i class="ti ti-eye text-white"></i>
                                    </a>
                                </div>
                                @can('edit internal itsm')
                                    <div class="action-btn me-2">
                                        <a href="#" data-url="{{ route('internal-itsm.edit', $ticket->id) }}" data-size="lg" data-ajax-popup="true"
                                            data-title="{{ __('Edit ITSM Ticket') }}" class="mx-3 btn btn-sm align-items-center bg-info">
                                            <i class="ti ti-pencil text-white"></i>
                                        </a>
                                    </div>
                                @endcan
                                @can('delete internal itsm')
                                    <div class="action-btn">
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['internal-itsm.destroy', $ticket->id], 'id' => 'delete-form-itsm-' . $ticket->id]) !!}
                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                           data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                           data-confirm-yes="document.getElementById('delete-form-itsm-{{ $ticket->id }}').submit();">
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
