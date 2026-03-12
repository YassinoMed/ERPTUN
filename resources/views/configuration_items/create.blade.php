{{ Form::open(['url' => 'configuration-items', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
<div class="col-md-6"><div class="form-group">{{ Form::label('name', __('Name'), ['class'=>'form-label']) }}<x-required></x-required>{{ Form::text('name', null, ['class'=>'form-control','required']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('ci_type', __('Type'), ['class'=>'form-label']) }}{{ Form::text('ci_type', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('status', __('Status'), ['class'=>'form-label']) }}{{ Form::select('status', $statuses, 'active', ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('criticality', __('Criticality'), ['class'=>'form-label']) }}{{ Form::select('criticality', $criticalities, null, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('asset_id', __('Linked Asset'), ['class'=>'form-label']) }}{{ Form::select('asset_id', $assets, null, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('owner_user_id', __('Owner'), ['class'=>'form-label']) }}{{ Form::select('owner_user_id', $users, null, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('asset_tag', __('Asset Tag'), ['class'=>'form-label']) }}{{ Form::text('asset_tag', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('serial_number', __('Serial Number'), ['class'=>'form-label']) }}{{ Form::text('serial_number', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('vendor_name', __('Vendor'), ['class'=>'form-label']) }}{{ Form::text('vendor_name', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('location', __('Location'), ['class'=>'form-label']) }}{{ Form::text('location', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('environment', __('Environment'), ['class'=>'form-label']) }}{{ Form::text('environment', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('acquired_at', __('Acquired At'), ['class'=>'form-label']) }}{{ Form::date('acquired_at', null, ['class'=>'form-control']) }}</div></div>
<div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class'=>'form-label']) }}{{ Form::textarea('notes', null, ['class'=>'form-control']) }}</div></div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
