@extends('layouts.admin')

@section('page-title')
    {{ __('Property Lease') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('property-leases.index') }}">{{ __('Property Leases') }}</a></li>
    <li class="breadcrumb-item">{{ $propertyLease->reference }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-4"><strong>{{ __('Reference') }}:</strong> {{ $propertyLease->reference }}</div>
                <div class="col-md-4"><strong>{{ __('Property') }}:</strong> {{ optional($propertyLease->property)->name ?: '-' }}</div>
                <div class="col-md-4"><strong>{{ __('Unit') }}:</strong> {{ optional($propertyLease->unit)->unit_code ?: '-' }}</div>
                <div class="col-md-4"><strong>{{ __('Tenant') }}:</strong> {{ optional($propertyLease->customer)->name ?: '-' }}</div>
                <div class="col-md-4"><strong>{{ __('Billing Cycle') }}:</strong> {{ __(ucfirst($propertyLease->billing_cycle)) }}</div>
                <div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $propertyLease->status))) }}</div>
                <div class="col-md-4"><strong>{{ __('Start Date') }}:</strong> {{ Auth::user()->dateFormat($propertyLease->start_date) }}</div>
                <div class="col-md-4"><strong>{{ __('End Date') }}:</strong> {{ $propertyLease->end_date ? Auth::user()->dateFormat($propertyLease->end_date) : '-' }}</div>
                <div class="col-md-4"><strong>{{ __('Renewal Date') }}:</strong> {{ $propertyLease->renewal_date ? Auth::user()->dateFormat($propertyLease->renewal_date) : '-' }}</div>
                <div class="col-md-4"><strong>{{ __('Rent Amount') }}:</strong> {{ Auth::user()->priceFormat($propertyLease->rent_amount) }}</div>
                <div class="col-md-4"><strong>{{ __('Deposit Amount') }}:</strong> {{ Auth::user()->priceFormat($propertyLease->deposit_amount) }}</div>
                <div class="col-12"><strong>{{ __('Notes') }}:</strong><p class="mb-0 mt-2">{{ $propertyLease->notes ?: '-' }}</p></div>
            </div>
        </div>
    </div>
@endsection
