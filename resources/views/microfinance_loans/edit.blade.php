<form method="POST" action="{{ route('microfinance-loans.update', $microfinanceLoan) }}">@csrf @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Customer') }}</label><select name="customer_id" class="form-control">@foreach($customers as $id => $label)<option value="{{ $id }}" @selected((int) $microfinanceLoan->customer_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Loan Number') }}</label><input type="text" name="loan_number" value="{{ $microfinanceLoan->loan_number }}" class="form-control" required></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Principal') }}</label><input type="number" step="0.01" name="principal_amount" value="{{ $microfinanceLoan->principal_amount }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Interest Rate') }}</label><input type="number" step="0.01" name="interest_rate" value="{{ $microfinanceLoan->interest_rate }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Installment') }}</label><input type="number" step="0.01" name="installment_amount" value="{{ $microfinanceLoan->installment_amount }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Start Date') }}</label><input type="date" name="start_date" value="{{ $microfinanceLoan->start_date }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Maturity Date') }}</label><input type="date" name="maturity_date" value="{{ $microfinanceLoan->maturity_date }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($microfinanceLoan->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Purpose') }}</label><input type="text" name="purpose" value="{{ $microfinanceLoan->purpose }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $microfinanceLoan->notes }}</textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>
