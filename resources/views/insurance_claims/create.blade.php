{{ Form::open(['url' => 'insurance-claims', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('insurance_policy_id', __('Policy'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('insurance_policy_id', $policies, null, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('claim_number', __('Claim Number'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('claim_number', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}{{ Form::select('customer_id', $customers, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('assigned_to', __('Assigned To'), ['class' => 'form-label']) }}{{ Form::select('assigned_to', $employees, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('incident_date', __('Incident Date'), ['class' => 'form-label']) }}{{ Form::date('incident_date', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('reported_date', __('Reported Date'), ['class' => 'form-label']) }}{{ Form::date('reported_date', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('incident_type', __('Incident Type'), ['class' => 'form-label']) }}{{ Form::text('incident_type', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('location', __('Location'), ['class' => 'form-label']) }}{{ Form::text('location', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('amount_claimed', __('Amount Claimed'), ['class' => 'form-label']) }}{{ Form::number('amount_claimed', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('amount_settled', __('Amount Settled'), ['class' => 'form-label']) }}{{ Form::number('amount_settled', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('priority', $priorities, 'medium', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'draft', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('description', __('Description'), ['class' => 'form-label']) }}{{ Form::textarea('description', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('resolution_notes', __('Resolution Notes'), ['class' => 'form-label']) }}{{ Form::textarea('resolution_notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
