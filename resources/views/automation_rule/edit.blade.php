@extends('layouts.admin')
@section('page-title', __('Edit Automation Rule'))
@section('content')<div class="card"><div class="card-body"><form method="POST" action="{{ route('automation-rules.update', $automationRule) }}">@method('PUT') @include('automation_rule._form')</form></div></div>@endsection
