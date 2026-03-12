@extends('layouts.admin')

@section('page-title')
    {{__('Manage Appointments')}}
@endsection
@section('page-subtitle')
    {{ __('Coordinate medical schedules, waiting list pressure and reminder execution from one screen.') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Appointments')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create medical appointment')
            <a href="#" data-size="lg" data-url="{{ route('medical-appointments.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Appointment')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @php
        $waitingListCount = $appointments->where('is_waiting_list', 1)->count();
        $confirmedCount = $appointments->where('status', 'confirmed')->count();
        $checkedInCount = $appointments->where('status', 'checked_in')->count();
        $reminderCount = $appointments->filter(function ($appointment) {
            return !empty($appointment->reminder_channel) && !empty($appointment->reminder_at);
        })->count();
    @endphp
    <div class="row">
        <div class="col-12 mb-4">
            <div class="ux-kpi-grid">
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Confirmed appointments') }}</span>
                    <strong class="ux-kpi-value">{{ $confirmedCount }}</strong>
                    <span class="ux-kpi-meta">{{ __('ready for care delivery') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Checked-in patients') }}</span>
                    <strong class="ux-kpi-value">{{ $checkedInCount }}</strong>
                    <span class="ux-kpi-meta">{{ __('already on site') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Waiting list') }}</span>
                    <strong class="ux-kpi-value">{{ $waitingListCount }}</strong>
                    <span class="ux-kpi-meta">{{ __('patients still pending slot') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Reminders configured') }}</span>
                    <strong class="ux-kpi-value">{{ $reminderCount }}</strong>
                    <span class="ux-kpi-meta">{{ __('email, SMS or WhatsApp') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Patient')}}</th>
                                <th>{{__('Doctor')}}</th>
                                <th>{{__('Start')}}</th>
                                <th>{{__('End')}}</th>
                                <th>{{__('Room')}}</th>
                                <th>{{__('Specialty')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Queue')}}</th>
                                <th>{{__('Reminder')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($appointments as $appointment)
                                <tr data-bulk-id="{{ $appointment->id }}">
                                    <td>
                                        @if($appointment->patient)
                                            <a href="{{ route('patients.show', $appointment->patient->id) }}" class="text-primary">
                                                {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $appointment->doctor ? $appointment->doctor->name : '-' }}</td>
                                    <td>{{ \Auth::user()->dateFormat($appointment->start_at) }} {{ \Auth::user()->timeFormat($appointment->start_at) }}</td>
                                    <td>{{ \Auth::user()->dateFormat($appointment->end_at) }} {{ \Auth::user()->timeFormat($appointment->end_at) }}</td>
                                    <td>{{ $appointment->room ?? '-' }}</td>
                                    <td>{{ $appointment->specialty ?? '-' }}</td>
                                    <td>{{ $appointment->appointment_type ?? '-' }}</td>
                                    <td>
                                        @if($appointment->is_waiting_list)
                                            {{ __('Waiting List') }}
                                        @elseif($appointment->queue_number)
                                            #{{ $appointment->queue_number }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($appointment->reminder_channel && $appointment->reminder_at)
                                            {{ strtoupper($appointment->reminder_channel) }} · {{ \Auth::user()->dateFormat($appointment->reminder_at) }} {{ \Auth::user()->timeFormat($appointment->reminder_at) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ ucwords(str_replace('_', ' ', $appointment->status)) }}</td>
                                    <td>
                                        <span>
                                            @can('edit medical appointment')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('medical-appointments.edit', $appointment->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Appointment')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete medical appointment')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['medical-appointments.destroy', $appointment->id],'id'=>'delete-form-'.$appointment->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$appointment->id}}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </span>
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
