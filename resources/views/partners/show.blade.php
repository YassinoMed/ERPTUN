@extends('layouts.admin')

@section('page-title')
    {{ __('Partner Details') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partners.index') }}">{{ __('Partners') }}</a></li>
    <li class="breadcrumb-item">{{ $partner->name }}</li>
@endsection

@section('content')
    <div class="row"><div class="col-12"><div class="card"><div class="card-body">
        <div class="row gy-3">
            <div class="col-md-4"><strong>{{ __('Code') }}:</strong> {{ $partner->partner_code }}</div>
            <div class="col-md-4"><strong>{{ __('Type') }}:</strong> {{ __(ucfirst($partner->partner_type)) }}</div>
            <div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($partner->status)) }}</div>
            <div class="col-md-4"><strong>{{ __('Contact') }}:</strong> {{ $partner->contact_name ?: '-' }}</div>
            <div class="col-md-4"><strong>{{ __('Email') }}:</strong> {{ $partner->email ?: '-' }}</div>
            <div class="col-md-4"><strong>{{ __('Phone') }}:</strong> {{ $partner->phone ?: '-' }}</div>
            <div class="col-md-6"><strong>{{ __('Customer') }}:</strong> {{ optional($partner->customer)->name ?: '-' }}</div>
            <div class="col-md-6"><strong>{{ __('Vendor') }}:</strong> {{ optional($partner->vender)->name ?: '-' }}</div>
            <div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $partner->notes ?: '-' }}</div></div>
        </div>
    </div></div></div></div>
@endsection
