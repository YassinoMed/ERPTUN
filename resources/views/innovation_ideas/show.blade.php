@extends('layouts.admin')

@section('page-title')
    {{ __('Innovation Idea Detail') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('innovation-ideas.index') }}">{{ __('Innovation Ideas') }}</a></li>
    <li class="breadcrumb-item">{{ __('Detail') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>{{ $innovationIdea->title }}</h5></div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Category') }}:</strong> {{ $innovationIdea->category ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Submitted By') }}:</strong> {{ optional($innovationIdea->submitter)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Priority') }}:</strong> {{ __(ucfirst($innovationIdea->priority)) }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $innovationIdea->status))) }}</div>
                        <div class="col-md-6"><strong>{{ __('Expected Value') }}:</strong> {{ Auth::user()->priceFormat($innovationIdea->expected_value) }}</div>
                        <div class="col-12"><strong>{{ __('Description') }}:</strong><p class="text-muted mb-0">{{ $innovationIdea->description ?: '-' }}</p></div>
                        <div class="col-12"><strong>{{ __('Business Case') }}:</strong><p class="text-muted mb-0">{{ $innovationIdea->business_case ?: '-' }}</p></div>
                        <div class="col-12"><strong>{{ __('Implementation Notes') }}:</strong><p class="text-muted mb-0">{{ $innovationIdea->implementation_notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
