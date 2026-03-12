@extends('layouts.admin')
@section('page-title')
    {{ __('Shift Teams') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item">{{ __('Shift Teams') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create production shift team')
            <a href="#" data-size="lg" data-url="{{ route('production.shift-teams.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create Shift Team') }}"
                class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Supervisor') }}</th>
                            <th>{{ __('Hours') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shiftTeams as $shiftTeam)
                            <tr>
                                <td>{{ $shiftTeam->name }}</td>
                                <td>{{ $shiftTeam->supervisor?->name ?: '-' }}</td>
                                <td>{{ $shiftTeam->start_time ?: '--:--' }} - {{ $shiftTeam->end_time ?: '--:--' }}</td>
                                <td>{{ ucfirst($shiftTeam->status) }}</td>
                                <td class="Action">
                                    @can('edit production shift team')
                                        <div class="action-btn me-2">
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                data-url="{{ route('production.shift-teams.edit', $shiftTeam->id) }}" data-ajax-popup="true" data-size="lg"
                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}" data-title="{{ __('Edit Shift Team') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                    @endcan
                                    @can('delete production shift team')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['production.shift-teams.destroy', $shiftTeam->id], 'id' => 'delete-form-shift-' . $shiftTeam->id]) !!}
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
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
