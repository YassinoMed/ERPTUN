@extends('layouts.admin')

@section('page-title')
    {{ __('Insurance Claim') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('insurance-claims.index') }}">{{ __('Insurance Claims') }}</a></li>
    <li class="breadcrumb-item">{{ $insuranceClaim->claim_number }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Claim Number') }}:</strong> {{ $insuranceClaim->claim_number }}</div>
                        <div class="col-md-6"><strong>{{ __('Policy') }}:</strong> {{ optional($insuranceClaim->policy)->policy_name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Customer') }}:</strong> {{ optional($insuranceClaim->customer)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Assignee') }}:</strong> {{ optional($insuranceClaim->assignee)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Incident Date') }}:</strong> {{ $insuranceClaim->incident_date ? Auth::user()->dateFormat($insuranceClaim->incident_date) : '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Reported Date') }}:</strong> {{ $insuranceClaim->reported_date ? Auth::user()->dateFormat($insuranceClaim->reported_date) : '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Priority') }}:</strong> {{ __(ucfirst($insuranceClaim->priority)) }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $insuranceClaim->status))) }}</div>
                        <div class="col-md-6"><strong>{{ __('Amount Claimed') }}:</strong> {{ Auth::user()->priceFormat($insuranceClaim->amount_claimed) }}</div>
                        <div class="col-md-6"><strong>{{ __('Amount Settled') }}:</strong> {{ Auth::user()->priceFormat($insuranceClaim->amount_settled) }}</div>
                        <div class="col-md-6"><strong>{{ __('Incident Type') }}:</strong> {{ $insuranceClaim->incident_type ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Location') }}:</strong> {{ $insuranceClaim->location ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Description') }}:</strong><p class="mb-0 mt-2">{{ $insuranceClaim->description ?: '-' }}</p></div>
                        <div class="col-md-12"><strong>{{ __('Resolution Notes') }}:</strong><p class="mb-0 mt-2">{{ $insuranceClaim->resolution_notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-12"><strong>{{ __('Policy Number') }}:</strong> {{ optional($insuranceClaim->policy)->policy_number ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Provider') }}:</strong> {{ optional($insuranceClaim->policy)->provider_name ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Coverage Type') }}:</strong> {{ optional($insuranceClaim->policy)->coverage_type ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Insured Party') }}:</strong> {{ optional($insuranceClaim->policy)->insured_party ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Insured Asset') }}:</strong> {{ optional($insuranceClaim->policy)->insured_asset ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
