{{ Form::open(['url' => 'managed-properties', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('name', __('Property Name'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('property_code', __('Property Code'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('property_code', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('property_type', __('Property Type'), ['class' => 'form-label']) }}{{ Form::text('property_type', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('manager_employee_id', __('Manager'), ['class' => 'form-label']) }}{{ Form::select('manager_employee_id', $employees, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('country', __('Country'), ['class' => 'form-label']) }}{{ Form::text('country', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('city', __('City'), ['class' => 'form-label']) }}{{ Form::text('city', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'draft', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('address', __('Address'), ['class' => 'form-label']) }}{{ Form::text('address', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
