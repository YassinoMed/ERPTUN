@extends('layouts.admin')

@section('page-title')
    {{ __('Property Unit') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('property-units.index') }}">{{ __('Property Units') }}</a></li>
    <li class="breadcrumb-item">{{ $propertyUnit->unit_code }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-12"><strong>{{ __('Unit') }}:</strong> {{ $propertyUnit->unit_code }}</div>
                        <div class="col-md-12"><strong>{{ __('Property') }}:</strong> {{ optional($propertyUnit->property)->name ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Floor') }}:</strong> {{ $propertyUnit->floor ?: '-' }}</div>
                        <div class="col-md-6"><strong>{{ __('Area') }}:</strong> {{ $propertyUnit->area }}</div>
                        <div class="col-md-6"><strong>{{ __('Monthly Rent') }}:</strong> {{ Auth::user()->priceFormat($propertyUnit->monthly_rent) }}</div>
                        <div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($propertyUnit->status)) }}</div>
                        <div class="col-md-12"><strong>{{ __('Notes') }}:</strong><p class="mb-0 mt-2">{{ $propertyUnit->notes ?: '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Lease History') }}</h5></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Reference') }}</th><th>{{ __('Tenant') }}</th><th>{{ __('Period') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @forelse($propertyUnit->leases as $lease)
                                <tr>
                                    <td>{{ $lease->reference }}</td>
                                    <td>{{ optional($lease->customer)->name ?: '-' }}</td>
                                    <td>{{ Auth::user()->dateFormat($lease->start_date) }} - {{ $lease->end_date ? Auth::user()->dateFormat($lease->end_date) : '-' }}</td>
                                    <td>{{ __(ucfirst(str_replace('_', ' ', $lease->status))) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">{{ __('No leases attached to this unit.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
