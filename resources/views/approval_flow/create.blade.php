@extends('layouts.admin')
@section('page-title', __('Create Approval Flow'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('approval-flows.index') }}">{{ __('Approval Flows') }}</a></li>
<li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('approval-flows.store') }}">
        @include('approval_flow._form')
    </form>
</div></div>
@endsection
