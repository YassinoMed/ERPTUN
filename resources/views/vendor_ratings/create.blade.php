<form method="POST" action="{{ route('vendor-ratings.store') }}">@csrf
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Vendor') }}</label><select name="vender_id" class="form-control">@foreach($venders as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Period') }}</label><input type="text" name="period_label" class="form-control" required></div>
        <div class="col-md-3 form-group"><label class="form-label">{{ __('Quality') }}</label><input type="number" step="0.01" name="quality_score" class="form-control"></div>
        <div class="col-md-3 form-group"><label class="form-label">{{ __('Delivery') }}</label><input type="number" step="0.01" name="delivery_score" class="form-control"></div>
        <div class="col-md-3 form-group"><label class="form-label">{{ __('Cost') }}</label><input type="number" step="0.01" name="cost_score" class="form-control"></div>
        <div class="col-md-3 form-group"><label class="form-label">{{ __('Service') }}</label><input type="number" step="0.01" name="service_score" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
    </div>
    <div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Create') }}</button></div>
</form>
