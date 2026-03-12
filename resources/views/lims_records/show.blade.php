@extends('layouts.admin')
@section('page-title', __('LIMS Record'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('lims-records.index') }}">{{ __('LIMS Records') }}</a></li><li class="breadcrumb-item">{{ $limsRecord->sample_code }}</li>
@endsection
@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body"><div class="row gy-3"><div class="col-md-4"><strong>{{ __('Sample Code') }}:</strong> {{ $limsRecord->sample_code }}</div><div class="col-md-4"><strong>{{ __('Test Type') }}:</strong> {{ $limsRecord->test_type }}</div><div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst(str_replace('_',' ',$limsRecord->status))) }}</div><div class="col-md-6"><strong>{{ __('Product') }}:</strong> {{ optional($limsRecord->productService)->name ?: '-' }}</div><div class="col-md-6"><strong>{{ __('Approver') }}:</strong> {{ optional($limsRecord->approver)->name ?: '-' }}</div><div class="col-md-6"><strong>{{ __('Lot Reference') }}:</strong> {{ $limsRecord->lot_reference ?: '-' }}</div><div class="col-md-6"><strong>{{ __('Tested At') }}:</strong> {{ $limsRecord->tested_at ? Auth::user()->dateFormat($limsRecord->tested_at) : '-' }}</div><div class="col-12"><strong>{{ __('Result Summary') }}:</strong><div class="text-muted">{{ $limsRecord->result_summary ?: '-' }}</div></div><div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $limsRecord->notes ?: '-' }}</div></div></div></div></div></div></div>
@endsection
