{{ Form::open(['url' => route('production.shift-teams.store'), 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">{{ Form::label('name', __('Name'), ['class' => 'form-label']) }}{{ Form::text('name', null, ['class' => 'form-control', 'required']) }}</div>
        <div class="form-group col-md-6">{{ Form::label('code', __('Code'), ['class' => 'form-label']) }}{{ Form::text('code', null, ['class' => 'form-control']) }}</div>
        <div class="form-group col-md-6">{{ Form::label('supervisor_id', __('Supervisor'), ['class' => 'form-label']) }}{{ Form::select('supervisor_id', $supervisors, null, ['class' => 'form-control', 'placeholder' => __('Select Supervisor')]) }}</div>
        <div class="form-group col-md-3">{{ Form::label('start_time', __('Start Time'), ['class' => 'form-label']) }}{{ Form::time('start_time', null, ['class' => 'form-control']) }}</div>
        <div class="form-group col-md-3">{{ Form::label('end_time', __('End Time'), ['class' => 'form-label']) }}{{ Form::time('end_time', null, ['class' => 'form-control']) }}</div>
        <div class="form-group col-md-6">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['active' => __('Active'), 'inactive' => __('Inactive')], 'active', ['class' => 'form-control']) }}</div>
        <div class="form-group col-md-12">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) }}</div>
    </div>
</div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
