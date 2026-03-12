@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Board Meetings') }}
@endsection
@section('page-subtitle')
    {{ __('Track upcoming governance sessions, attendance load and meeting execution from one board view.') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Board Meetings') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create board meeting')
            <a href="#" data-url="{{ route('board-meeting.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Board Meeting') }}" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @php
        $plannedMeetings = $meetings->where('status', 'planned')->count();
        $completedMeetings = $meetings->where('status', 'completed')->count();
        $cancelledMeetings = $meetings->where('status', 'cancelled')->count();
        $attendeeVolume = $meetings->sum('attendees_count');
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="ux-kpi-grid mb-4">
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Planned meetings') }}</span>
                    <strong class="ux-kpi-value">{{ $plannedMeetings }}</strong>
                    <span class="ux-kpi-meta">{{ __('scheduled governance sessions') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Completed meetings') }}</span>
                    <strong class="ux-kpi-value">{{ $completedMeetings }}</strong>
                    <span class="ux-kpi-meta">{{ __('already closed') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Cancelled meetings') }}</span>
                    <strong class="ux-kpi-value">{{ $cancelledMeetings }}</strong>
                    <span class="ux-kpi-meta">{{ __('sessions interrupted') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Attendee volume') }}</span>
                    <strong class="ux-kpi-value">{{ $attendeeVolume }}</strong>
                    <span class="ux-kpi-meta">{{ __('total participant assignments') }}</span>
                </div>
            </div>
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Time') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Attendees') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="font-style">
                                @foreach ($meetings as $meeting)
                                    <tr data-bulk-id="{{ $meeting->id }}">
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ !empty($meeting->branch) ? $meeting->branch->name : '-' }}</td>
                                        <td>{{ \Auth::user()->dateFormat($meeting->meeting_date) }}</td>
                                        <td>{{ \Auth::user()->timeFormat($meeting->meeting_time) }}</td>
                                        <td>
                                            <span class="badge bg-light-primary p-2 px-3 rounded">
                                                {{ __(\App\Models\BoardMeeting::$status[$meeting->status] ?? ucfirst($meeting->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $meeting->attendees_count }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('board-meeting.show', $meeting->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning"
                                                    data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit board meeting')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('board-meeting/' . $meeting->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Board Meeting') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info"
                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete board meeting')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['board-meeting.destroy', $meeting->id], 'id' => 'delete-form-' . $meeting->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $meeting->id }}').submit();">
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
