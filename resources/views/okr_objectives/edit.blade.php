@extends('layouts.admin')
@section('page-title', __('Edit Objective'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('okr-objectives.index') }}">{{ __('OKR Workspace') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('okr-objectives.update', $okrObjective) }}">@csrf @method('PUT') @include('okr_objectives._form')<div class="text-end"><a href="{{ route('okr-objectives.show', $okrObjective) }}" class="btn btn-light">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button></div></form></div></div>
@endsection
