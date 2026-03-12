@extends('layouts.admin')

@section('page-title')
    {{ __('Cap Table Detail') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cap-table.index') }}">{{ __('Cap Table') }}</a></li>
    <li class="breadcrumb-item">{{ __('Detail') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>{{ $capTable->holder_name }}</h5></div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ __(ucfirst($capTable->holder_type)) }}</div>
                        <div class="col-md-6"><strong>{{ __('Share Class') }}:</strong> {{ $capTable->share_class ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Share Count') }}:</strong> {{ $capTable->share_count }}</div>
                        <div class="col-md-6"><strong>{{ __('Issue Price') }}:</strong> {{ Auth::user()->priceFormat($capTable->issue_price) }}</div>
                        <div class="col-md-6"><strong>{{ __('Ownership %') }}:</strong> {{ $capTable->ownership_percentage }}</div>
                        <div class="col-md-6"><strong>{{ __('Voting %') }}:</strong> {{ $capTable->voting_percentage }}</div>
                        <div class="col-md-6"><strong>{{ __('Email') }}:</strong> {{ $capTable->contact_email ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Phone') }}:</strong> {{ $capTable->contact_phone ?: '-' }}</div>
                        <div class="col-12"><strong>{{ __('Notes') }}:</strong><p class="text-muted mb-0">{{ $capTable->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
