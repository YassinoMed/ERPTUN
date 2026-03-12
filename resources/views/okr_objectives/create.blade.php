@extends('layouts.admin')
@section('page-title', __('Create Objective'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('okr-objectives.index') }}">{{ __('OKR Workspace') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection
@section('content')
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('okr-objectives.store') }}">@csrf @include('okr_objectives._form')<div class="text-end"><a href="{{ route('okr-objectives.index') }}" class="btn btn-light">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary">{{ __('Create') }}</button></div></form></div></div>
@endsection
