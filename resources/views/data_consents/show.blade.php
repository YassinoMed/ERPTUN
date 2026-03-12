@extends('layouts.admin')
@section('page-title'){{ __('Data Consent') }}@endsection
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('data-consents.index') }}">{{ __('Data Consents') }}</a></li><li class="breadcrumb-item">{{ $dataConsent->subject_name }}</li>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row gy-2">
<div class="col-md-6"><strong>{{ __('Subject') }}:</strong> {{ $dataConsent->subject_name }}</div>
<div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ $dataConsent->subject_type ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Reference') }}:</strong> {{ $dataConsent->subject_reference ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Purpose') }}:</strong> {{ $dataConsent->purpose ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Channel') }}:</strong> {{ $dataConsent->channel ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ ucfirst($dataConsent->status) }}</div>
<div class="col-md-6"><strong>{{ __('Consented At') }}:</strong> {{ $dataConsent->consented_at ? Auth::user()->dateFormat($dataConsent->consented_at) : '-' }}</div>
<div class="col-md-6"><strong>{{ __('Expires At') }}:</strong> {{ $dataConsent->expires_at ? Auth::user()->dateFormat($dataConsent->expires_at) : '-' }}</div>
<div class="col-12"><strong>{{ __('Evidence Reference') }}:</strong> {{ $dataConsent->evidence_reference ?: '-' }}</div>
<div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $dataConsent->notes ?: '-' }}</div></div>
</div></div></div>
@endsection
