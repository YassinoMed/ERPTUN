@extends('layouts.admin')
@section('page-title', __('Schedule Export'))
@section('content')
<div class="card"><div class="card-body"><form method="POST" action="{{ route('core.exports.store') }}">@csrf
<div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Module') }}</label><select name="module" class="form-control" required><option value="customers">customers</option><option value="venders">venders</option><option value="patients">patients</option><option value="product_services">product_services</option></select></div>
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Format') }}</label><select name="format" class="form-control"><option value="csv">CSV</option><option value="json">JSON</option></select></div>
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Scheduled For') }}</label><input type="datetime-local" name="scheduled_for" class="form-control"></div>
</div>
<div class="alert alert-info small">{{ __('Rollback support is currently enabled for customers, venders, patients and product services imports.') }}</div>
<button class="btn btn-primary">{{ __('Schedule Export') }}</button>
</form></div></div>
@endsection
