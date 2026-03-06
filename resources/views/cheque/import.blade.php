{{ Form::open(['route' => 'cheques.import', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('file', __('Import File'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::file('file', ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-12 text-muted">
            {{ __('CSV format: ID, Beneficiary, Amount, Currency, Issue Date, Due Date, Cheque Number, Status, Status Date') }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Import')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
