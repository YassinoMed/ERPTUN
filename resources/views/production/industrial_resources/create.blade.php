{{ Form::open(['url' => route('production.resources.store'), 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('type', ['site' => __('Site'), 'workshop' => __('Workshop'), 'line' => __('Line'), 'station' => __('Station')], 'site', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('parent_id', __('Parent Resource'), ['class' => 'form-label']) }}
            {{ Form::select('parent_id', $parentResources, null, ['class' => 'form-control', 'placeholder' => __('Select Parent Resource')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}
            {{ Form::text('code', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}
            {{ Form::select('branch_id', $branches, null, ['class' => 'form-control', 'placeholder' => __('Select Branch')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('manager_id', __('Manager'), ['class' => 'form-label']) }}
            {{ Form::select('manager_id', $managers, null, ['class' => 'form-control', 'placeholder' => __('Select Manager')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', ['active' => __('Active'), 'inactive' => __('Inactive'), 'maintenance' => __('Maintenance')], 'active', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('capacity_hours_per_day', __('Hours / Day'), ['class' => 'form-label']) }}
            {{ Form::number('capacity_hours_per_day', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) }}
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('capacity_workers', __('Workers'), ['class' => 'form-label']) }}
            {{ Form::number('capacity_workers', 0, ['class' => 'form-control', 'min' => 0]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
