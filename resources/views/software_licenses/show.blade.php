@extends('layouts.admin')
@section('page-title'){{ __('Software License') }}@endsection
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('software-licenses.index') }}">{{ __('Software Licenses') }}</a></li><li class="breadcrumb-item">{{ $softwareLicense->name }}</li>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row gy-2">
<div class="col-md-6"><strong>{{ __('Name') }}:</strong> {{ $softwareLicense->name }}</div>
<div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ ucfirst($softwareLicense->status) }}</div>
<div class="col-md-6"><strong>{{ __('Vendor') }}:</strong> {{ $softwareLicense->vendor_name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ $softwareLicense->license_type ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Assigned User') }}:</strong> {{ optional($softwareLicense->assignedUser)->name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Configuration Item') }}:</strong> {{ optional($softwareLicense->configurationItem)->name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Seats') }}:</strong> {{ $softwareLicense->seats_used }}/{{ $softwareLicense->seats_total }}</div>
<div class="col-md-6"><strong>{{ __('Renewal Date') }}:</strong> {{ $softwareLicense->renewal_date ? Auth::user()->dateFormat($softwareLicense->renewal_date) : '-' }}</div>
<div class="col-md-6"><strong>{{ __('Cost') }}:</strong> {{ Auth::user()->priceFormat($softwareLicense->cost) }}</div>
<div class="col-md-6"><strong>{{ __('License Key') }}:</strong> {{ $softwareLicense->license_key ?: '-' }}</div>
<div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $softwareLicense->notes ?: '-' }}</div></div>
</div></div></div>
@endsection
