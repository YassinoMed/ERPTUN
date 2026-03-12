@extends('layouts.admin')

@section('page-title')
    {{ __('Medical Specialties') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical.operations.index') }}">{{ __('Advanced Medical Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Specialties') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Maintain the specialty catalog, responsible heads and the operational footprint of clinical disciplines.') }}
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header"><h5>{{ __('Specialty Register') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Head') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicalSpecialties as $specialty)
                        <tr>
                            <td>{{ $specialty->name }}</td>
                            <td>{{ $specialty->code }}</td>
                            <td>{{ $specialty->department_name ?: '-' }}</td>
                            <td>{{ $specialty->head_name ?: '-' }}</td>
                            <td>{{ ucfirst($specialty->status ?: 'active') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>{{ __('Recent Clinical Demand') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Appointment') }}</th>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        <tr>
                            <td>#{{ $appointment->id }}</td>
                            <td>{{ optional($appointment->patient)->first_name }} {{ optional($appointment->patient)->last_name }}</td>
                            <td>{{ $appointment->start_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $appointment->appointment_type ?: __('Consultation') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $appointment->status ?? 'scheduled')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
