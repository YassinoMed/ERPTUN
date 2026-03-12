<form method="POST" action="{{ route('lims-records.update', $limsRecord) }}">@csrf @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Sample Code') }}</label><input type="text" name="sample_code" value="{{ $limsRecord->sample_code }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Test Type') }}</label><input type="text" name="test_type" value="{{ $limsRecord->test_type }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Product') }}</label><select name="product_service_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($products as $id => $label)<option value="{{ $id }}" @selected((int) $limsRecord->product_service_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Approver') }}</label><select name="approved_by" class="form-control"><option value="">{{ __('None') }}</option>@foreach($employees as $id => $label)<option value="{{ $id }}" @selected((int) $limsRecord->approved_by === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Lot Reference') }}</label><input type="text" name="lot_reference" value="{{ $limsRecord->lot_reference }}" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($limsRecord->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Tested At') }}</label><input type="datetime-local" name="tested_at" value="{{ $limsRecord->tested_at ? \Carbon\Carbon::parse($limsRecord->tested_at)->format('Y-m-d\\TH:i') : '' }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Result Summary') }}</label><textarea name="result_summary" class="form-control" rows="3">{{ $limsRecord->result_summary }}</textarea></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $limsRecord->notes }}</textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>
