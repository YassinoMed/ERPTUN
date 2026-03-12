@extends('layouts.admin')

@section('page-title')
    {{ __('Insurance Policy') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('insurance-policies.index') }}">{{ __('Insurance Policies') }}</a></li>
    <li class="breadcrumb-item">{{ $insurancePolicy->policy_name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-12"><strong>{{ __('Policy') }}:</strong> {{ $insurancePolicy->policy_name }}</div>
                        <div class="col-md-6"><strong>{{ __('Number') }}:</strong> {{ $insurancePolicy->policy_number }}</div>
                        <div class="col-md-6"><strong>{{ __('Provider') }}:</strong> {{ $insurancePolicy->provider_name }}</div>
                        <div class="col-md-6"><strong>{{ __('Coverage Type') }}:</strong> {{ $insurancePolicy->coverage_type ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_', ' ', $insurancePolicy->status))) }}</div>
                        <div class="col-md-6"><strong>{{ __('Insured Party') }}:</strong> {{ $insurancePolicy->insured_party ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Insured Asset') }}:</strong> {{ $insurancePolicy->insured_asset ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Premium') }}:</strong> {{ Auth::user()->priceFormat($insurancePolicy->premium_amount) }}</div>
                        <div class="col-md-6"><strong>{{ __('Coverage Amount') }}:</strong> {{ Auth::user()->priceFormat($insurancePolicy->coverage_amount) }}</div>
                        <div class="col-md-6"><strong>{{ __('Start Date') }}:</strong> {{ $insurancePolicy->start_date ? Auth::user()->dateFormat($insurancePolicy->start_date) : '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('End Date') }}:</strong> {{ $insurancePolicy->end_date ? Auth::user()->dateFormat($insurancePolicy->end_date) : '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Notes') }}:</strong><p class="mb-0 mt-2">{{ $insurancePolicy->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Linked Claims') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Claim') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Assignee') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($insurancePolicy->claims as $claim)
                                    <tr>
                                        <td>{{ $claim->claim_number }}</td>
                                        <td>{{ optional($claim->customer)->name ?: '-' }}</td>
                                        <td>{{ Auth::user()->priceFormat($claim->amount_claimed) }}</td>
                                        <td>{{ __(ucfirst(str_replace('_', ' ', $claim->status))) }}</td>
                                        <td>{{ optional($claim->assignee)->name ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('No claims linked to this policy yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
