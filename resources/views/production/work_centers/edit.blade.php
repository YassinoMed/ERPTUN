{{ Form::model($workCenter, ['route' => ['production.work-centers.update', $workCenter->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::select('type', ['machine' => __('Machine'), 'workshop' => __('Workshop'), 'employee' => __('Employee')], null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('industrial_resource_id', __('Industrial Resource'), ['class' => 'form-label']) }}
            {{ Form::select('industrial_resource_id', $resources, null, ['class' => 'form-control', 'placeholder' => __('Select Industrial Resource')]) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('machine_code', __('Machine Code'), ['class' => 'form-label']) }}
            {{ Form::text('machine_code', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('cost_per_hour', __('Cost / Hour'), ['class' => 'form-label']) }}
            {{ Form::number('cost_per_hour', null, ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('capacity_hours_per_day', __('Capacity Hours / Day'), ['class' => 'form-label']) }}
            {{ Form::number('capacity_hours_per_day', null, ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('capacity_workers', __('Capacity Workers'), ['class' => 'form-label']) }}
            {{ Form::number('capacity_workers', null, ['class' => 'form-control', 'min' => 0]) }}
        </div>
        <div class="form-group col-md-12">
            <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_bottleneck" id="is_bottleneck" {{ $workCenter->is_bottleneck ? 'checked' : '' }}>
                <label class="form-check-label" for="is_bottleneck">{{ __('Bottleneck Resource') }}</label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
