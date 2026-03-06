@extends('layouts.admin')

@section('page-title')
    {{__('Manage Appointments')}}
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
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
                                <th>{{__('Reminder')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
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
                                    <td>
                                        @if($appointment->reminder_channel && $appointment->reminder_at)
                                            {{ strtoupper($appointment->reminder_channel) }} · {{ \Auth::user()->dateFormat($appointment->reminder_at) }} {{ \Auth::user()->timeFormat($appointment->reminder_at) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($appointment->status) }}</td>
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
