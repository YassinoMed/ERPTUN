@extends('layouts.admin')

@section('page-title')
    {{ __('ITSM Ticket') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('internal-itsm.index') }}">{{ __('Internal ITSM') }}</a></li>
    <li class="breadcrumb-item">{{ $ticket->ticket_code }}</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Ticket Details') }}</h5></div>
                <div class="card-body">
                    <div class="row gy-2">
                        <div class="col-12"><strong>{{ $ticket->subject }}</strong><div class="text-muted">{{ $ticket->description }}</div></div>
                        <div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ ucfirst(str_replace('_', ' ', $ticket->ticket_type ?: '-')) }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ $ticket->status }}</div>
                        <div class="col-md-6"><strong>{{ __('Priority') }}:</strong> {{ $ticket->priority }}</div>
                        <div class="col-md-6"><strong>{{ __('Assignee') }}:</strong> {{ optional($ticket->assignUser)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Impact') }}:</strong> {{ ucfirst($ticket->impact_level ?: '-') }}</div>
                        <div class="col-md-6"><strong>{{ __('Urgency') }}:</strong> {{ ucfirst($ticket->urgency_level ?: '-') }}</div>
                        <div class="col-md-6"><strong>{{ __('Category') }}:</strong> {{ optional($ticket->category)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('CI') }}:</strong> {{ optional($ticket->configurationItem)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Due') }}:</strong> {{ $ticket->resolution_due_at ? Auth::user()->dateFormat($ticket->resolution_due_at) : '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Resolved') }}:</strong> {{ $ticket->resolved_at ? Auth::user()->dateFormat($ticket->resolved_at) : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Replies') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3 mb-4">
                        @forelse($replies as $reply)
                            <div class="border rounded p-3">
                                <strong>{{ optional($reply->users)->name ?: __('Unknown') }}</strong>
                                <div class="text-muted small">{{ optional($reply->created_at)->diffForHumans() }}</div>
                                <div class="mt-2">{{ $reply->description }}</div>
                            </div>
                        @empty
                            <div class="text-muted">{{ __('No replies yet.') }}</div>
                        @endforelse
                    </div>
                    {{ Form::open(['route' => ['internal-itsm.reply', $ticket->id], 'method' => 'post']) }}
                    <div class="form-group">
                        {{ Form::label('description', __('Post Reply'), ['class' => 'form-label']) }}
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'required']) }}
                    </div>
                    <div class="text-end mt-3"><input type="submit" value="{{ __('Send Reply') }}" class="btn btn-primary"></div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
