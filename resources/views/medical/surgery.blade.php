@extends('layouts.admin')

@section('page-title')
    {{ __('Surgery Board') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical.operations.index') }}">{{ __('Advanced Medical Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Surgery Board') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Follow theatre workload, procedure status and surgical backlog from one perioperative board.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Planned') }}</span><strong class="ux-kpi-value">{{ $kpis['planned'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('In progress') }}</span><strong class="ux-kpi-value">{{ $kpis['in_progress'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Completed') }}</span><strong class="ux-kpi-value">{{ $kpis['completed'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Cancelled') }}</span><strong class="ux-kpi-value">{{ $kpis['cancelled'] }}</strong></div>
    </div>

    <div class="card">
        <div class="card-header"><h5>{{ __('Perioperative Schedule') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Procedure') }}</th>
                        <th>{{ __('Surgeon') }}</th>
                        <th>{{ __('Theatre') }}</th>
                        <th>{{ __('Scheduled') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Update') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surgeries as $procedure)
                        <tr>
                            <td>{{ optional($procedure->patient)->first_name }} {{ optional($procedure->patient)->last_name }}</td>
                            <td>{{ $procedure->procedure_name }}</td>
                            <td>{{ $procedure->surgeon_name ?: '-' }}</td>
                            <td>{{ $procedure->theatre_name ?: '-' }}</td>
                            <td>{{ $procedure->scheduled_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $procedure->status)) }}</td>
                            <td>
                                <form action="{{ route('medical.operations.surgical-procedures.status', $procedure->id) }}" method="post" class="d-flex gap-2">
                                    @csrf
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="planned" @selected($procedure->status === 'planned')>{{ __('Planned') }}</option>
                                        <option value="in_progress" @selected($procedure->status === 'in_progress')>{{ __('In Progress') }}</option>
                                        <option value="completed" @selected($procedure->status === 'completed')>{{ __('Completed') }}</option>
                                        <option value="cancelled" @selected($procedure->status === 'cancelled')>{{ __('Cancelled') }}</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary">{{ __('Save') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
