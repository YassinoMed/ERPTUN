@extends('layouts.admin')
@section('page-title'){{ __('Security Incident') }}@endsection
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('security-incidents.index') }}">{{ __('Security Incidents') }}</a></li><li class="breadcrumb-item">{{ $securityIncident->incident_reference }}</li>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row gy-2">
<div class="col-md-6"><strong>{{ __('Title') }}:</strong> {{ $securityIncident->title }}</div>
<div class="col-md-6"><strong>{{ __('Reference') }}:</strong> {{ $securityIncident->incident_reference }}</div>
<div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ $securityIncident->incident_type ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Severity') }}:</strong> {{ ucfirst($securityIncident->severity) }}</div>
<div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ ucfirst($securityIncident->status) }}</div>
<div class="col-md-6"><strong>{{ __('Affected Module') }}:</strong> {{ $securityIncident->affected_module ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Reported By') }}:</strong> {{ optional($securityIncident->reporter)->name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Owner') }}:</strong> {{ optional($securityIncident->owner)->name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Detected At') }}:</strong> {{ $securityIncident->detected_at ? Auth::user()->dateFormat($securityIncident->detected_at) : '-' }}</div>
<div class="col-12"><strong>{{ __('Summary') }}:</strong><div class="text-muted">{{ $securityIncident->summary ?: '-' }}</div></div>
<div class="col-md-6"><strong>{{ __('Containment Actions') }}:</strong><div class="text-muted">{{ $securityIncident->containment_actions ?: '-' }}</div></div>
<div class="col-md-6"><strong>{{ __('Resolution Notes') }}:</strong><div class="text-muted">{{ $securityIncident->resolution_notes ?: '-' }}</div></div>
</div></div></div>
@endsection
