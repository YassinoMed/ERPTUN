{{ Form::open(['url' => 'property-leases', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('managed_property_id', __('Property'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('managed_property_id', $properties, null, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('property_unit_id', __('Unit'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('property_unit_id', $units, null, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('customer_id', __('Tenant Customer'), ['class' => 'form-label']) }}{{ Form::select('customer_id', $customers, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('reference', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('billing_cycle', __('Billing Cycle'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('billing_cycle', $billingCycles, 'monthly', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'draft', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('renewal_date', __('Renewal Date'), ['class' => 'form-label']) }}{{ Form::date('renewal_date', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::date('start_date', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}{{ Form::date('end_date', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('rent_amount', __('Rent Amount'), ['class' => 'form-label']) }}{{ Form::number('rent_amount', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('deposit_amount', __('Deposit Amount'), ['class' => 'form-label']) }}{{ Form::number('deposit_amount', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
