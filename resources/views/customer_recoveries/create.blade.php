{{ Form::open(['url' => 'customer-recoveries', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('customer_id', $customers, null, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('invoice_id', __('Invoice'), ['class' => 'form-label']) }}{{ Form::select('invoice_id', $invoices, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}{{ Form::text('reference', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('assigned_to', __('Assigned To'), ['class' => 'form-label']) }}{{ Form::select('assigned_to', $employees, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('stage', $stages, 'new', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('priority', $priorities, 'medium', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'open', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('due_amount', __('Due Amount'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::number('due_amount', 0, ['class' => 'form-control', 'step' => '0.0001', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('last_contact_date', __('Last Contact Date'), ['class' => 'form-label']) }}{{ Form::date('last_contact_date', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('next_follow_up_date', __('Next Follow Up Date'), ['class' => 'form-label']) }}{{ Form::date('next_follow_up_date', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
