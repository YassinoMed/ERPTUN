@extends('layouts.admin')
@section('page-title'){{ __('Configuration Item') }}@endsection
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li><li class="breadcrumb-item"><a href="{{ route('configuration-items.index') }}">{{ __('CMDB') }}</a></li><li class="breadcrumb-item">{{ $configurationItem->name }}</li>@endsection
@section('content')
<div class="card"><div class="card-body"><div class="row gy-2">
<div class="col-md-6"><strong>{{ __('Name') }}:</strong> {{ $configurationItem->name }}</div>
<div class="col-md-6"><strong>{{ __('Type') }}:</strong> {{ $configurationItem->ci_type ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Status') }}:</strong> {{ ucfirst($configurationItem->status) }}</div>
<div class="col-md-6"><strong>{{ __('Criticality') }}:</strong> {{ ucfirst($configurationItem->criticality ?: '-') }}</div>
<div class="col-md-6"><strong>{{ __('Owner') }}:</strong> {{ optional($configurationItem->owner)->name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Asset') }}:</strong> {{ optional($configurationItem->asset)->name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Asset Tag') }}:</strong> {{ $configurationItem->asset_tag ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Serial Number') }}:</strong> {{ $configurationItem->serial_number ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Location') }}:</strong> {{ $configurationItem->location ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Environment') }}:</strong> {{ $configurationItem->environment ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Vendor') }}:</strong> {{ $configurationItem->vendor_name ?: '-' }}</div>
<div class="col-md-6"><strong>{{ __('Acquired At') }}:</strong> {{ $configurationItem->acquired_at ? Auth::user()->dateFormat($configurationItem->acquired_at) : '-' }}</div>
<div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $configurationItem->notes ?: '-' }}</div></div>
</div></div></div>
@endsection
