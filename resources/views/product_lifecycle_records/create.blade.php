<form method="POST" action="{{ route('product-lifecycle-records.store') }}">@csrf
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Product') }}</label><select name="product_service_id" class="form-control">@foreach($products as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Owner') }}</label><select name="owner_employee_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($employees as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Stage') }}</label><select name="stage" class="form-control">@foreach($stages as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Effective Date') }}</label><input type="date" name="effective_date" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Compliance Status') }}</label><input type="text" name="compliance_status" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Create') }}</button></div>
</form>
