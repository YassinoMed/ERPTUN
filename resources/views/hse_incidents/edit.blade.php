<form method="POST" action="{{ route('hse-incidents.update', $hseIncident) }}">@csrf @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Incident Code') }}</label><input type="text" name="incident_code" value="{{ $hseIncident->incident_code }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Title') }}</label><input type="text" name="title" value="{{ $hseIncident->title }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Category') }}</label><input type="text" name="category" value="{{ $hseIncident->category }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Reported By') }}</label><select name="reported_by_employee_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($employees as $id => $label)<option value="{{ $id }}" @selected((int) $hseIncident->reported_by_employee_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Severity') }}</label><select name="severity" class="form-control">@foreach($severities as $key => $label)<option value="{{ $key }}" @selected($hseIncident->severity === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($hseIncident->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Occurred On') }}</label><input type="date" name="occurred_on" value="{{ $hseIncident->occurred_on }}" class="form-control"></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Location') }}</label><input type="text" name="location" value="{{ $hseIncident->location }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Actions') }}</label><textarea name="actions" class="form-control" rows="3">{{ $hseIncident->actions }}</textarea></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $hseIncident->notes }}</textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>
