<form method="POST" action="{{ route('partners.update', $partner) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Partner Code') }}</label><input type="text" name="partner_code" value="{{ $partner->partner_code }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Name') }}</label><input type="text" name="name" value="{{ $partner->name }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Type') }}</label><select name="partner_type" class="form-control">@foreach($types as $key => $label)<option value="{{ $key }}" @selected($partner->partner_type === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($partner->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Customer') }}</label><select name="customer_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($customers as $id => $label)<option value="{{ $id }}" @selected((int) $partner->customer_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Vendor') }}</label><select name="vender_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($venders as $id => $label)<option value="{{ $id }}" @selected((int) $partner->vender_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Contact Name') }}</label><input type="text" name="contact_name" value="{{ $partner->contact_name }}" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Email') }}</label><input type="email" name="email" value="{{ $partner->email }}" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Phone') }}</label><input type="text" name="phone" value="{{ $partner->phone }}" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Website') }}</label><input type="text" name="website" value="{{ $partner->website }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $partner->notes }}</textarea></div>
    </div>
    <div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>
