@extends('layouts.admin')

@section('page-title')
    {{ __('Board Meeting Detail') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('board-meeting.index') }}">{{ __('Board Meetings') }}</a></li>
    <li class="breadcrumb-item">{{ __('Detail') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('edit board meeting')
            <a href="#" data-url="{{ URL::to('board-meeting/' . $boardMeeting->id . '/edit') }}" data-size="lg"
                data-ajax-popup="true" data-title="{{ __('Edit Board Meeting') }}"
                class="btn btn-sm btn-primary me-2">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ $boardMeeting->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Branch') }}:</strong></p>
                            <p class="text-muted">{{ !empty($boardMeeting->branch) ? $boardMeeting->branch->name : '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>{{ __('Date') }}:</strong></p>
                            <p class="text-muted">{{ \Auth::user()->dateFormat($boardMeeting->meeting_date) }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>{{ __('Time') }}:</strong></p>
                            <p class="text-muted">{{ \Auth::user()->timeFormat($boardMeeting->meeting_time) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Location') }}:</strong></p>
                            <p class="text-muted">{{ $boardMeeting->location ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('Status') }}:</strong></p>
                            <p class="text-muted">{{ __(\App\Models\BoardMeeting::$status[$boardMeeting->status] ?? ucfirst($boardMeeting->status)) }}</p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1"><strong>{{ __('Meeting Link') }}:</strong></p>
                            <p class="text-muted">
                                @if ($boardMeeting->meeting_link)
                                    <a href="{{ $boardMeeting->meeting_link }}" target="_blank">{{ $boardMeeting->meeting_link }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1"><strong>{{ __('Agenda') }}:</strong></p>
                            <p class="text-muted">{{ $boardMeeting->agenda ?: '-' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1"><strong>{{ __('Minutes') }}:</strong></p>
                            <p class="text-muted">{{ $boardMeeting->minutes ?: '-' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1"><strong>{{ __('Decision Summary') }}:</strong></p>
                            <p class="text-muted">{{ $boardMeeting->resolution_summary ?: '-' }}</p>
                        </div>
                        <div class="col-12">
                            <p class="mb-1"><strong>{{ __('External Guests') }}:</strong></p>
                            <p class="text-muted">{!! nl2br(e($boardMeeting->external_guests ?: '-')) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Board Members') }}</h5>
                </div>
                <div class="card-body">
                    @forelse ($boardMeeting->attendees as $attendee)
                        <div class="border-bottom pb-2 mb-2">
                            <strong>{{ !empty($attendee->employee) ? $attendee->employee->name : __('Deleted employee') }}</strong>
                            <div class="text-muted">{{ !empty($attendee->employee) ? $attendee->employee->email : '-' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No board members assigned.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
