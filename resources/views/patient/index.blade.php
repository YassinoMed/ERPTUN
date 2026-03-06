@extends('layouts.admin')

@section('page-title')
    {{__('Manage Patients')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Patients')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create patient')
            <a href="#" data-size="lg" data-url="{{ route('patients.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Patient')}}" class="btn btn-sm btn-primary">
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
                                <th>{{__('CIN')}}</th>
                                <th>{{__('CNAM')}}</th>
                                <th>{{__('Phone')}}</th>
                                <th>{{__('Created')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($patients as $patient)
                                <tr>
                                    <td>
                                        <a href="{{ route('patients.show', $patient->id) }}" class="text-primary">
                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                        </a>
                                    </td>
                                    <td>{{ $patient->cin ?? '-' }}</td>
                                    <td>{{ $patient->cnam_number ?? '-' }}</td>
                                    <td>{{ $patient->phone ?? '-' }}</td>
                                    <td>{{ \Auth::user()->dateFormat($patient->created_at) }}</td>
                                    <td>
                                        <span>
                                            @can('show patient')
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('patients.show', $patient->id) }}" class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="{{__('View')}}">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('edit patient')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('patients.edit', $patient->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Patient')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete patient')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['patients.destroy', $patient->id],'id'=>'delete-form-'.$patient->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$patient->id}}').submit();">
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
