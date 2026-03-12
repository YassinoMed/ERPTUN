@extends('layouts.admin')

@section('page-title')
    {{ __('Property') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('managed-properties.index') }}">{{ __('Properties') }}</a></li>
    <li class="breadcrumb-item">{{ $managedProperty->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-12"><strong>{{ __('Property') }}:</strong> {{ $managedProperty->name }}</div>
                        <div class="col-md-12"><strong>{{ __('Code') }}:</strong> {{ $managedProperty->property_code }}</div>
                        <div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ $managedProperty->property_type ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($managedProperty->status)) }}</div>
                        <div class="col-md-6"><strong>{{ __('Manager') }}:</strong> {{ optional($managedProperty->manager)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('City') }}:</strong> {{ $managedProperty->city ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Address') }}:</strong> {{ $managedProperty->address ?: '-' }}</div>
                        <div class="col-md-12"><strong>{{ __('Notes') }}:</strong><p class="mb-0 mt-2">{{ $managedProperty->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Units') }}</h5></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Unit') }}</th><th>{{ __('Floor') }}</th><th>{{ __('Rent') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @forelse($managedProperty->units as $unit)
                                <tr>
                                    <td>{{ $unit->unit_code }}</td>
                                    <td>{{ $unit->floor ?: '-' }}</td>
                                    <td>{{ Auth::user()->priceFormat($unit->monthly_rent) }}</td>
                                    <td>{{ __(ucfirst($unit->status)) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">{{ __('No units created yet.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Leases') }}</h5></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Unit') }}</th><th>{{ __('Tenant') }}</th><th>{{ __('Rent') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @forelse($managedProperty->leases as $lease)
                                <tr>
                                    <td>{{ $lease->reference }}</td>
                                    <td>{{ optional($lease->unit)->unit_code ?: '-' }}</td>
                                    <td>{{ optional($lease->customer)->name ?: '-' }}</td>
                                    <td>{{ Auth::user()->priceFormat($lease->rent_amount) }}</td>
                                    <td>{{ __(ucfirst(str_replace('_', ' ', $lease->status))) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">{{ __('No leases linked yet.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
