{{ Form::open(['url' => 'property-units', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('managed_property_id', __('Property'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('managed_property_id', $properties, null, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('unit_code', __('Unit Code'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('unit_code', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('floor', __('Floor'), ['class' => 'form-label']) }}{{ Form::text('floor', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('area', __('Area'), ['class' => 'form-label']) }}{{ Form::number('area', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('monthly_rent', __('Monthly Rent'), ['class' => 'form-label']) }}{{ Form::number('monthly_rent', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'available', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
