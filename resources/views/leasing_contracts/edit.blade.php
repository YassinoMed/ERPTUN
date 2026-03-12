<form method="POST" action="{{ route('leasing-contracts.update', $leasingContract) }}">@csrf @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Customer') }}</label><select name="customer_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($customers as $id => $label)<option value="{{ $id }}" @selected((int) $leasingContract->customer_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Contract Number') }}</label><input type="text" name="contract_number" value="{{ $leasingContract->contract_number }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Asset Name') }}</label><input type="text" name="asset_name" value="{{ $leasingContract->asset_name }}" class="form-control" required></div>
        <div class="col-md-3 form-group"><label class="form-label">{{ __('Lease Amount') }}</label><input type="number" step="0.01" name="lease_amount" value="{{ $leasingContract->lease_amount }}" class="form-control"></div>
        <div class="col-md-3 form-group"><label class="form-label">{{ __('Residual Amount') }}</label><input type="number" step="0.01" name="residual_amount" value="{{ $leasingContract->residual_amount }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Start Date') }}</label><input type="date" name="start_date" value="{{ $leasingContract->start_date }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('End Date') }}</label><input type="date" name="end_date" value="{{ $leasingContract->end_date }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($leasingContract->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Payment Frequency') }}</label><input type="text" name="payment_frequency" value="{{ $leasingContract->payment_frequency }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $leasingContract->notes }}</textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>
