@extends('layouts.admin')

@section('page-title')
    {{ __('Recovery Case Detail') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('customer-recoveries.index') }}">{{ __('Customer Recoveries') }}</a></li>
    <li class="breadcrumb-item">{{ __('Detail') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>{{ $customerRecovery->reference ?: ('REC-' . $customerRecovery->id) }}</h5></div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Customer') }}:</strong> {{ optional($customerRecovery->customer)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Invoice') }}:</strong> {{ optional($customerRecovery->invoice)->invoice_id ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Stage') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $customerRecovery->stage))) }}</div>
                        <div class="col-md-6"><strong>{{ __('Priority') }}:</strong> {{ __(ucfirst($customerRecovery->priority)) }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $customerRecovery->status))) }}</div>
                        <div class="col-md-6"><strong>{{ __('Assigned To') }}:</strong> {{ optional($customerRecovery->assignee)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Due Amount') }}:</strong> {{ Auth::user()->priceFormat($customerRecovery->due_amount) }}</div>
                        <div class="col-md-6"><strong>{{ __('Last Contact') }}:</strong> {{ $customerRecovery->last_contact_date ? Auth::user()->dateFormat($customerRecovery->last_contact_date) : '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Next Follow Up') }}:</strong> {{ $customerRecovery->next_follow_up_date ? Auth::user()->dateFormat($customerRecovery->next_follow_up_date) : '-' }}</div>
                        <div class="col-12"><strong>{{ __('Notes') }}:</strong><p class="text-muted mb-0">{{ $customerRecovery->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
