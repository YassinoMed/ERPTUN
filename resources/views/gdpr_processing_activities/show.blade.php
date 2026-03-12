@extends('layouts.admin')
@section('page-title'){{ __('GDPR Activity') }}@endsection
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('gdpr-activities.index') }}">{{ __('GDPR Register') }}</a></li><li class="breadcrumb-item">{{ $gdprActivity->activity_code }}</li>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row gy-2">
<div class="col-md-6"><strong>{{ __('Activity') }}:</strong> {{ $gdprActivity->activity_name }}</div>
<div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ ucfirst(str_replace('_', ' ', $gdprActivity->status)) }}</div>
<div class="col-md-6"><strong>{{ __('Data Category') }}:</strong> {{ $gdprActivity->data_category ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Purpose') }}:</strong> {{ $gdprActivity->purpose ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Lawful Basis') }}:</strong> {{ $gdprActivity->lawful_basis ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Processor') }}:</strong> {{ $gdprActivity->processor_name ?: '-' }}</div>
<div class="col-12"><strong>{{ __('Retention Period') }}:</strong> {{ $gdprActivity->retention_period ?: '-' }}</div>
<div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $gdprActivity->notes ?: '-' }}</div></div>
</div></div></div>
@endsection
