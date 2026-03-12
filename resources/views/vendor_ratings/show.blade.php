@extends('layouts.admin')
@section('page-title', __('Vendor Rating Details'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendor-ratings.index') }}">{{ __('Vendor Ratings') }}</a></li><li class="breadcrumb-item">{{ optional($vendorRating->vender)->name ?: __('Rating') }}</li>
@endsection
@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body"><div class="row gy-3"><div class="col-md-4"><strong>{{ __('Vendor') }}:</strong> {{ optional($vendorRating->vender)->name ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Period') }}:</strong> {{ $vendorRating->period_label }}</div><div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($vendorRating->status)) }}</div><div class="col-md-3"><strong>{{ __('Quality') }}:</strong> {{ $vendorRating->quality_score }}</div><div class="col-md-3"><strong>{{ __('Delivery') }}:</strong> {{ $vendorRating->delivery_score }}</div><div class="col-md-3"><strong>{{ __('Cost') }}:</strong> {{ $vendorRating->cost_score }}</div><div class="col-md-3"><strong>{{ __('Service') }}:</strong> {{ $vendorRating->service_score }}</div><div class="col-md-4"><strong>{{ __('Total') }}:</strong> {{ $vendorRating->total_score }}</div><div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $vendorRating->notes ?: '-' }}</div></div></div></div></div></div></div>
@endsection
