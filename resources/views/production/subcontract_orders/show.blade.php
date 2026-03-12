@extends('layouts.admin')
@section('page-title'){{ $subcontractOrder->reference ?: ('SUB-'.$subcontractOrder->id) }}@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('production.subcontract-orders.index') }}">{{ __('Subcontract Orders') }}</a></li>
    <li class="breadcrumb-item">{{ $subcontractOrder->reference ?: ('SUB-'.$subcontractOrder->id) }}</li>
@endsection
@section('content')
    <div class="card"><div class="card-body">
        <p>{{ __('Production Order') }}: {{ $subcontractOrder->order?->order_number ?: '-' }}</p>
        <p>{{ __('Routing Step') }}: {{ $subcontractOrder->step?->name ?: '-' }}</p>
        <p>{{ __('Vendor') }}: {{ $subcontractOrder->vendor?->name ?: '-' }}</p>
        <p>{{ __('Status') }}: {{ ucfirst(str_replace('_',' ', $subcontractOrder->status)) }}</p>
        <p>{{ __('Quantity') }}: {{ $subcontractOrder->quantity }}</p>
        <p>{{ __('Unit Cost') }}: {{ \Auth::user()->priceFormat($subcontractOrder->unit_cost) }}</p>
        <p class="mb-0">{{ __('Quality Notes') }}: {{ $subcontractOrder->quality_notes ?: '-' }}</p>
    </div></div>
@endsection
