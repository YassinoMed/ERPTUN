@extends('layouts.admin')

@section('page-title')
    {{ __('Visitor Detail') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('visitors.index') }}">{{ __('Visitors') }}</a></li>
    <li class="breadcrumb-item">{{ __('Detail') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>{{ $visitor->visitor_name }}</h5></div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Company') }}:</strong> {{ $visitor->company_name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Host') }}:</strong> {{ optional($visitor->host)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Email') }}:</strong> {{ $visitor->email ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Phone') }}:</strong> {{ $visitor->phone ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Visit Date') }}:</strong> {{ Auth::user()->dateFormat($visitor->visit_date) }}</div>
                        <div class="col-md-6"><strong>{{ __('Visit Time') }}:</strong> {{ $visitor->visit_time ? Auth::user()->timeFormat($visitor->visit_time) : '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $visitor->status))) }}</div>
                        <div class="col-md-6"><strong>{{ __('Badge Number') }}:</strong> {{ $visitor->badge_number ?: '-' }}</div>
                        <div class="col-12"><strong>{{ __('Purpose') }}:</strong><p class="text-muted mb-0">{{ $visitor->purpose ?: '-' }}</p></div>
                        <div class="col-12"><strong>{{ __('Notes') }}:</strong><p class="text-muted mb-0">{{ $visitor->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
