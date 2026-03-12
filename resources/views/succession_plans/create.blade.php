<form method="POST" action="{{ route('succession-plans.store') }}">@csrf
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Employee') }}</label><select name="employee_id" class="form-control">@foreach($employees as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Successor') }}</label><select name="successor_employee_id" class="form-control"><option value="">{{ __('None') }}</option>@foreach($employees as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Readiness') }}</label><select name="readiness_level" class="form-control">@foreach($levels as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Risk') }}</label><select name="risk_level" class="form-control">@foreach($levels as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}">{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Target Date') }}</label><input type="date" name="target_date" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Create') }}</button></div>
</form>
