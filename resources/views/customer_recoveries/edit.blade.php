{{ Form::model($customerRecovery, ['route' => ['customer-recoveries.update', $customerRecovery->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('customer_id', $customers, $customerRecovery->customer_id, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('invoice_id', __('Invoice'), ['class' => 'form-label']) }}{{ Form::select('invoice_id', $invoices, $customerRecovery->invoice_id, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}{{ Form::text('reference', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('assigned_to', __('Assigned To'), ['class' => 'form-label']) }}{{ Form::select('assigned_to', $employees, $customerRecovery->assigned_to, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('stage', $stages, $customerRecovery->stage, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('priority', $priorities, $customerRecovery->priority, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, $customerRecovery->status, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('due_amount', __('Due Amount'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::number('due_amount', null, ['class' => 'form-control', 'step' => '0.0001', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('last_contact_date', __('Last Contact Date'), ['class' => 'form-label']) }}{{ Form::date('last_contact_date', $customerRecovery->last_contact_date, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('next_follow_up_date', __('Next Follow Up Date'), ['class' => 'form-label']) }}{{ Form::date('next_follow_up_date', $customerRecovery->next_follow_up_date, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
