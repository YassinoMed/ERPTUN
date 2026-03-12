@extends('layouts.admin')
@section('page-title', __('Succession Plan'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('succession-plans.index') }}">{{ __('Succession Plans') }}</a></li><li class="breadcrumb-item">{{ optional($successionPlan->employee)->name ?: __('Plan') }}</li>
@endsection
@section('content')
<div class="row"><div class="col-12"><div class="card"><div class="card-body"><div class="row gy-3"><div class="col-md-4"><strong>{{ __('Employee') }}:</strong> {{ optional($successionPlan->employee)->name ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Successor') }}:</strong> {{ optional($successionPlan->successor)->name ?: '-' }}</div><div class="col-md-4"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($successionPlan->status)) }}</div><div class="col-md-4"><strong>{{ __('Readiness') }}:</strong> {{ __(ucfirst($successionPlan->readiness_level)) }}</div><div class="col-md-4"><strong>{{ __('Risk') }}:</strong> {{ __(ucfirst($successionPlan->risk_level)) }}</div><div class="col-md-4"><strong>{{ __('Target Date') }}:</strong> {{ $successionPlan->target_date ? Auth::user()->dateFormat($successionPlan->target_date) : '-' }}</div><div class="col-12"><strong>{{ __('Notes') }}:</strong><div class="text-muted">{{ $successionPlan->notes ?: '-' }}</div></div></div></div></div></div></div>
@endsection
