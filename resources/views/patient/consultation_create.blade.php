{{ Form::open(['route' => ['patients.consultations.store', $patient->id], 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('consultation_date', __('Consultation Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('consultation_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('doctor_name', __('Doctor Name'), ['class' => 'form-label']) }}
                {{ Form::text('doctor_name', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
                {{ Form::text('title', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('diagnosis', __('Diagnosis'), ['class' => 'form-label']) }}
                {{ Form::text('diagnosis', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('next_visit_date', __('Next Visit Date'), ['class' => 'form-label']) }}
                {{ Form::date('next_visit_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
