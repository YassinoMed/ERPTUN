@extends('layouts.admin')
@section('page-title', __('Edit Approval Flow'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('approval-flows.index') }}">{{ __('Approval Flows') }}</a></li>
<li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('approval-flows.update', $approvalFlow) }}">
        @method('PUT')
        @include('approval_flow._form')
    </form>
</div></div>
@endsection
