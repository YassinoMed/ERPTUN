@extends('layouts.admin')
@section('page-title', __('Create Saved Report'))
@section('content')
<div class="card"><div class="card-body"><form method="POST" action="{{ route('core.reports.store') }}">@csrf
<div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Name') }}</label><input type="text" name="name" class="form-control" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Report Type') }}</label><select name="report_type" class="form-control"><option value="invoices">Invoices</option><option value="customers">Customers</option><option value="purchases">Purchases</option></select></div>
    <div class="col-md-4 mb-3"><div class="form-check mt-4"><input type="checkbox" class="form-check-input" name="is_shared" value="1"><label class="form-check-label">{{ __('Shared') }}</label></div></div>
    <div class="col-md-12 mb-3"><label class="form-label">{{ __('Columns') }}</label><input type="text" name="columns[]" class="form-control mb-2" placeholder="id"><input type="text" name="columns[]" class="form-control" placeholder="invoice_id / name / status"></div>
</div>
<button class="btn btn-primary">{{ __('Create Report') }}</button>
</form></div></div>
@endsection
