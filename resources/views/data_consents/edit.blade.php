{{ Form::model($dataConsent, ['route' => ['data-consents.update', $dataConsent->id], 'method' => 'PUT']) }}
<div class="modal-body"><div class="row">
<div class="col-md-6"><div class="form-group">{{ Form::label('subject_name', __('Subject Name'), ['class'=>'form-label']) }}<x-required></x-required>{{ Form::text('subject_name', null, ['class'=>'form-control','required']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('subject_type', __('Subject Type'), ['class'=>'form-label']) }}{{ Form::text('subject_type', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('subject_reference', __('Subject Reference'), ['class'=>'form-label']) }}{{ Form::text('subject_reference', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('purpose', __('Purpose'), ['class'=>'form-label']) }}{{ Form::text('purpose', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('channel', __('Channel'), ['class'=>'form-label']) }}{{ Form::text('channel', null, ['class'=>'form-control']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class'=>'form-label']) }}{{ Form::select('status', $statuses, $dataConsent->status, ['class'=>'form-control select']) }}</div></div>
<div class="col-md-4"><div class="form-group">{{ Form::label('consented_at', __('Consented At'), ['class'=>'form-label']) }}{{ Form::date('consented_at', optional($dataConsent->consented_at)->format('Y-m-d'), ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('expires_at', __('Expires At'), ['class'=>'form-label']) }}{{ Form::date('expires_at', optional($dataConsent->expires_at)->format('Y-m-d'), ['class'=>'form-control']) }}</div></div>
<div class="col-md-6"><div class="form-group">{{ Form::label('evidence_reference', __('Evidence Reference'), ['class'=>'form-label']) }}{{ Form::text('evidence_reference', null, ['class'=>'form-control']) }}</div></div>
<div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class'=>'form-label']) }}{{ Form::textarea('notes', null, ['class'=>'form-control']) }}</div></div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Update') }}" class="btn btn-primary"></div>
{{ Form::close() }}
