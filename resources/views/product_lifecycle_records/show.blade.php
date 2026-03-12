@extends('layouts.admin')
@section('page-title', __('Lifecycle Record'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('product-lifecycle-records.index') }}">{{ __('Product Lifecycle') }}</a></li><li class="breadcrumb-item">{{ optional($productLifecycleRecord->productService)->name ?: __('Record') }}</li>
@endsection
@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body"><div class="row gy-3"><div class="col-md-4"><strong>{{ __('Product') }}:</strong> {{ optional($productLifecycleRecord->productService)->name ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Stage') }}:</strong> {{ __(ucfirst($productLifecycleRecord->stage)) }}</div><div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($productLifecycleRecord->status)) }}</div><div class="col-md-4"><strong>{{ __('Owner') }}:</strong> {{ optional($productLifecycleRecord->owner)->name ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Effective Date') }}:</strong> {{ $productLifecycleRecord->effective_date ? Auth::user()->dateFormat($productLifecycleRecord->effective_date) : '-' }}</div><div class="col-md-4"><strong>{{ __('Compliance') }}:</strong> {{ $productLifecycleRecord->compliance_status ?: '-' }}</div><div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $productLifecycleRecord->notes ?: '-' }}</div></div></div></div></div></div></div>
@endsection
