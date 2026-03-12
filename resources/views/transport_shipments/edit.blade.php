<form method="POST" action="{{ route('transport-shipments.update', $transportShipment) }}">@csrf @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Reference') }}</label><input type="text" name="reference" value="{{ $transportShipment->reference }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Customer') }}</label><select name="customer_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($customers as $id => $label)<option value="{{ $id }}" @selected((int) $transportShipment->customer_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Origin') }}</label><input type="text" name="origin" value="{{ $transportShipment->origin }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Destination') }}</label><input type="text" name="destination" value="{{ $transportShipment->destination }}" class="form-control" required></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Vehicle Number') }}</label><input type="text" name="vehicle_number" value="{{ $transportShipment->vehicle_number }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Driver Name') }}</label><input type="text" name="driver_name" value="{{ $transportShipment->driver_name }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($transportShipment->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Departure Date') }}</label><input type="date" name="departure_date" value="{{ $transportShipment->departure_date }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Arrival Date') }}</label><input type="date" name="arrival_date" value="{{ $transportShipment->arrival_date }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Freight Amount') }}</label><input type="number" step="0.01" name="freight_amount" value="{{ $transportShipment->freight_amount }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $transportShipment->notes }}</textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>
