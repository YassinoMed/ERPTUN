{{ Form::open(['url' => 'visitors', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('visitor_name', __('Visitor Name'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('visitor_name', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('company_name', __('Company'), ['class' => 'form-label']) }}{{ Form::text('company_name', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('email', __('Email'), ['class' => 'form-label']) }}{{ Form::email('email', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('phone', __('Phone'), ['class' => 'form-label']) }}{{ Form::text('phone', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('host_employee_id', __('Host Employee'), ['class' => 'form-label']) }}{{ Form::select('host_employee_id', $employees, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('visit_date', __('Visit Date'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::date('visit_date', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('visit_time', __('Visit Time'), ['class' => 'form-label']) }}{{ Form::time('visit_time', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('purpose', __('Purpose'), ['class' => 'form-label']) }}{{ Form::text('purpose', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'expected', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('badge_number', __('Badge Number'), ['class' => 'form-label']) }}{{ Form::text('badge_number', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
