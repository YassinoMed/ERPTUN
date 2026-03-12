{{ Form::model($securityIncident, ['route' => ['security-incidents.update', $securityIncident->id], 'method' => 'PUT']) }}
<div class="modal-body"><div class="row">
<div class="col-md-6"><div class="form-group">{{ Form::label('title', __('Title'), ['class'=>'form-label']) }}<x-required></x-required>{{ Form::text('title', null, ['class'=>'form-control','required']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('incident_type', __('Type'), ['class'=>'form-label']) }}{{ Form::text('incident_type', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('status', __('Status'), ['class'=>'form-label']) }}{{ Form::select('status', $statuses, $securityIncident->status, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('severity', __('Severity'), ['class'=>'form-label']) }}{{ Form::select('severity', $severities, $securityIncident->severity, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('reported_by', __('Reported By'), ['class'=>'form-label']) }}{{ Form::select('reported_by', $users, $securityIncident->reported_by, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('owner_id', __('Owner'), ['class'=>'form-label']) }}{{ Form::select('owner_id', $users, $securityIncident->owner_id, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('affected_module', __('Affected Module'), ['class'=>'form-label']) }}{{ Form::text('affected_module', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('detected_at', __('Detected At'), ['class'=>'form-label']) }}{{ Form::datetimeLocal('detected_at', optional($securityIncident->detected_at)->format('Y-m-d\TH:i'), ['class'=>'form-control']) }}</div></div>
<div class="col-12"><div class="form-group">{{ Form::label('summary', __('Summary'), ['class'=>'form-label']) }}{{ Form::textarea('summary', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('containment_actions', __('Containment Actions'), ['class'=>'form-label']) }}{{ Form::textarea('containment_actions', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('resolution_notes', __('Resolution Notes'), ['class'=>'form-label']) }}{{ Form::textarea('resolution_notes', null, ['class'=>'form-control']) }}</div></div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Update') }}" class="btn btn-primary"></div>
{{ Form::close() }}
