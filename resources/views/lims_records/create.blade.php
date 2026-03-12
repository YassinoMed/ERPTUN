<form method="POST" action="{{ route('lims-records.store') }}">@csrf
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Sample Code') }}</label><input type="text" name="sample_code" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Test Type') }}</label><input type="text" name="test_type" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Product') }}</label><select name="product_service_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($products as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Approver') }}</label><select name="approved_by" class="form-control"><option value="">{{ __('None') }}</option>@foreach($employees as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Lot Reference') }}</label><input type="text" name="lot_reference" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Tested At') }}</label><input type="datetime-local" name="tested_at" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Result Summary') }}</label><textarea name="result_summary" class="form-control" rows="3"></textarea></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Create') }}</button></div>
</form>
