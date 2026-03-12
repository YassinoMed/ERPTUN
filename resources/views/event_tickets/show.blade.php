@extends('layouts.admin')
@section('page-title', __('Event Ticket'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('event-tickets.index') }}">{{ __('Event Tickets') }}</a></li><li class="breadcrumb-item">{{ $eventTicket->ticket_code }}</li>
@endsection
@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body"><div class="row gy-3"><div class="col-md-4"><strong>{{ __('Ticket Code') }}:</strong> {{ $eventTicket->ticket_code }}</div><div class="col-md-4"><strong>{{ __('Event') }}:</strong> {{ optional($eventTicket->event)->title ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_',' ',$eventTicket->status))) }}</div><div class="col-md-6"><strong>{{ __('Attendee') }}:</strong> {{ $eventTicket->attendee_name }}</div><div class="col-md-6"><strong>{{ __('Email') }}:</strong> {{ $eventTicket->attendee_email ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Price') }}:</strong> {{ Auth::user()->priceFormat($eventTicket->price) }}</div><div class="col-md-4"><strong>{{ __('Checked In At') }}:</strong> {{ $eventTicket->checked_in_at ? Auth::user()->dateFormat($eventTicket->checked_in_at) : '-' }}</div><div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $eventTicket->notes ?: '-' }}</div></div></div></div></div></div></div>
@endsection
