@extends('layouts.admin')
@section('page-title', __('HSE Incident'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('hse-incidents.index') }}">{{ __('HSE Incidents') }}</a></li><li class="breadcrumb-item">{{ $hseIncident->title }}</li>
@endsection
@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body"><div class="row gy-3"><div class="col-md-4"><strong>{{ __('Code') }}:</strong> {{ $hseIncident->incident_code }}</div><div class="col-md-4"><strong>{{ __('Category') }}:</strong> {{ $hseIncident->category }}</div><div class="col-md-4"><strong>{{ __('Severity') }}:</strong> {{ __(ucfirst($hseIncident->severity)) }}</div><div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($hseIncident->status)) }}</div><div class="col-md-4"><strong>{{ __('Occurred On') }}:</strong> {{ $hseIncident->occurred_on ? Auth::user()->dateFormat($hseIncident->occurred_on) : '-' }}</div><div class="col-md-4"><strong>{{ __('Reporter') }}:</strong> {{ optional($hseIncident->reporter)->name ?: '-' }}</div><div class="col-md-6"><strong>{{ __('Location') }}:</strong> {{ $hseIncident->location ?: '-' }}</div><div class="col-12"><strong>{{ __('Actions') }}:</strong><div class="text-muted">{{ $hseIncident->actions ?: '-' }}</div></div><div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $hseIncident->notes ?: '-' }}</div></div></div></div></div></div></div>
@endsection
