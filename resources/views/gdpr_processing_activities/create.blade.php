{{ Form::open(['url' => 'gdpr-activities', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
<div class="col-md-6"><div class="form-group">{{ Form::label('activity_name', __('Activity Name'), ['class'=>'form-label']) }}<x-required></x-required>{{ Form::text('activity_name', null, ['class'=>'form-control','required']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('status', __('Status'), ['class'=>'form-label']) }}{{ Form::select('status', $statuses, 'active', ['class'=>'form-control select']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('data_category', __('Data Category'), ['class'=>'form-label']) }}{{ Form::text('data_category', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('purpose', __('Purpose'), ['class'=>'form-label']) }}{{ Form::text('purpose', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('lawful_basis', __('Lawful Basis'), ['class'=>'form-label']) }}{{ Form::text('lawful_basis', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('processor_name', __('Processor'), ['class'=>'form-label']) }}{{ Form::text('processor_name', null, ['class'=>'form-control']) }}</div></div>
<div class="col-12"><div class="form-group">{{ Form::label('retention_period', __('Retention Period'), ['class'=>'form-label']) }}{{ Form::text('retention_period', null, ['class'=>'form-control']) }}</div></div>
<div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class'=>'form-label']) }}{{ Form::textarea('notes', null, ['class'=>'form-control']) }}</div></div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
