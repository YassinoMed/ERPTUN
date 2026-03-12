{{ Form::model($capTable, ['route' => ['cap-table.update', $capTable->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('holder_name', __('Holder Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('holder_name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('holder_type', __('Holder Type'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('holder_type', $holderTypes, $capTable->holder_type, ['class' => 'form-control select', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('share_class', __('Share Class'), ['class' => 'form-label']) }}
                {{ Form::text('share_class', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('share_count', __('Share Count'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('share_count', null, ['class' => 'form-control', 'step' => '0.0001', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('issue_price', __('Issue Price'), ['class' => 'form-label']) }}
                {{ Form::number('issue_price', null, ['class' => 'form-control', 'step' => '0.0001']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('ownership_percentage', __('Ownership %'), ['class' => 'form-label']) }}
                {{ Form::number('ownership_percentage', null, ['class' => 'form-control', 'step' => '0.0001']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('voting_percentage', __('Voting %'), ['class' => 'form-label']) }}
                {{ Form::number('voting_percentage', null, ['class' => 'form-control', 'step' => '0.0001']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('contact_email', __('Contact Email'), ['class' => 'form-label']) }}
                {{ Form::email('contact_email', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('contact_phone', __('Contact Phone'), ['class' => 'form-label']) }}
                {{ Form::text('contact_phone', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                {{ Form::textarea('notes', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
