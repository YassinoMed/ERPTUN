{{ Form::open(['url' => 'software-licenses', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
<div class="col-md-6"><div class="form-group">{{ Form::label('name', __('License Name'), ['class'=>'form-label']) }}<x-required></x-required>{{ Form::text('name', null, ['class'=>'form-control','required']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('license_type', __('Type'), ['class'=>'form-label']) }}{{ Form::text('license_type', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('status', __('Status'), ['class'=>'form-label']) }}{{ Form::select('status', $statuses, 'active', ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('vendor_name', __('Vendor'), ['class'=>'form-label']) }}{{ Form::text('vendor_name', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('configuration_item_id', __('Configuration Item'), ['class'=>'form-label']) }}{{ Form::select('configuration_item_id', $configurationItems, null, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('assigned_user_id', __('Assigned User'), ['class'=>'form-label']) }}{{ Form::select('assigned_user_id', $users, null, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('license_key', __('License Key'), ['class'=>'form-label']) }}{{ Form::text('license_key', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('seats_total', __('Seats Total'), ['class'=>'form-label']) }}{{ Form::number('seats_total', 1, ['class'=>'form-control','min'=>'1']) }}</div></div>
<div class="col-md-3"><div class="form-group">{{ Form::label('seats_used', __('Seats Used'), ['class'=>'form-label']) }}{{ Form::number('seats_used', 0, ['class'=>'form-control','min'=>'0']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('renewal_date', __('Renewal Date'), ['class'=>'form-label']) }}{{ Form::date('renewal_date', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('cost', __('Cost'), ['class'=>'form-label']) }}{{ Form::number('cost', 0, ['class'=>'form-control','step'=>'0.01','min'=>'0']) }}</div></div>
<div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class'=>'form-label']) }}{{ Form::textarea('notes', null, ['class'=>'form-control']) }}</div></div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
