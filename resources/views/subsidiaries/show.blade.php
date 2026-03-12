@extends('layouts.admin')

@section('page-title')
    {{ __('Subsidiary Detail') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subsidiaries.index') }}">{{ __('Subsidiaries') }}</a></li>
    <li class="breadcrumb-item">{{ __('Detail') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>{{ $subsidiary->name }}</h5></div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Registration Number') }}:</strong> {{ $subsidiary->registration_number ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Country') }}:</strong> {{ $subsidiary->country ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Currency') }}:</strong> {{ $subsidiary->currency ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Parent Company') }}:</strong> {{ $subsidiary->parent_company ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Ownership %') }}:</strong> {{ $subsidiary->ownership_percentage }}</div>
                        <div class="col-md-6"><strong>{{ __('Method') }}:</strong> {{ __(ucfirst($subsidiary->consolidation_method)) }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($subsidiary->status)) }}</div>
                        <div class="col-12"><strong>{{ __('Notes') }}:</strong><p class="text-muted mb-0">{{ $subsidiary->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
