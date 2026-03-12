{{ Form::model($insurancePolicy, ['route' => ['insurance-policies.update', $insurancePolicy->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('policy_name', __('Policy Name'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('policy_name', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('policy_number', __('Policy Number'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('policy_number', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('provider_name', __('Provider'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('provider_name', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('coverage_type', __('Coverage Type'), ['class' => 'form-label']) }}{{ Form::text('coverage_type', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('insured_party', __('Insured Party'), ['class' => 'form-label']) }}{{ Form::text('insured_party', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('insured_asset', __('Insured Asset'), ['class' => 'form-label']) }}{{ Form::text('insured_asset', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}{{ Form::date('start_date', $insurancePolicy->start_date, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}{{ Form::date('end_date', $insurancePolicy->end_date, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('premium_amount', __('Premium Amount'), ['class' => 'form-label']) }}{{ Form::number('premium_amount', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('coverage_amount', __('Coverage Amount'), ['class' => 'form-label']) }}{{ Form::number('coverage_amount', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, $insurancePolicy->status, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
