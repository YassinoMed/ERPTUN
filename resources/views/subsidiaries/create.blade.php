{{ Form::open(['url' => 'subsidiaries', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('registration_number', __('Registration Number'), ['class' => 'form-label']) }}{{ Form::text('registration_number', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('country', __('Country'), ['class' => 'form-label']) }}{{ Form::text('country', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('currency', __('Currency'), ['class' => 'form-label']) }}{{ Form::text('currency', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('ownership_percentage', __('Ownership %'), ['class' => 'form-label']) }}{{ Form::number('ownership_percentage', 0, ['class' => 'form-control', 'step' => '0.0001']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('parent_company', __('Parent Company'), ['class' => 'form-label']) }}{{ Form::text('parent_company', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('consolidation_method', __('Consolidation Method'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('consolidation_method', $methods, 'full', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'active', ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
