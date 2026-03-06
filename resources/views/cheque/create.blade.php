{{ Form::open(['route' => 'cheques.store', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('beneficiary_name', __('Beneficiary Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('beneficiary_name', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::number('amount', null, ['class' => 'form-control', 'step' => '0.001', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                {{ Form::label('currency', __('Currency'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('currency', 'TND', ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('amount_text', __('Amount In Words'), ['class' => 'form-label']) }}
                {{ Form::text('amount_text', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('issue_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
                {{ Form::date('due_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) }}
                {{ Form::text('bank_name', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('bank_agency', __('Bank Agency'), ['class' => 'form-label']) }}
                {{ Form::text('bank_agency', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('bank_account', __('Bank Account'), ['class' => 'form-label']) }}
                {{ Form::text('bank_account', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('rib', __('RIB'), ['class' => 'form-label']) }}
                {{ Form::text('rib', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('chequebook_number', __('Chequebook Number'), ['class' => 'form-label']) }}
                {{ Form::text('chequebook_number', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('cheque_number', __('Cheque Number'), ['class' => 'form-label']) }}
                {{ Form::text('cheque_number', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
