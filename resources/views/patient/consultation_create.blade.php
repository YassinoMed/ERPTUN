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
                {{ Form::label('reason_for_visit', __('Reason for Visit'), ['class' => 'form-label']) }}
                {{ Form::text('reason_for_visit', null, ['class' => 'form-control']) }}
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
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="form-group">
                {{ Form::label('temperature', __('Temperature (°C)'), ['class' => 'form-label']) }}
                {{ Form::number('temperature', null, ['class' => 'form-control', 'step' => '0.01']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="form-group">
                {{ Form::label('heart_rate', __('Heart Rate'), ['class' => 'form-label']) }}
                {{ Form::number('heart_rate', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="form-group">
                {{ Form::label('blood_pressure', __('Blood Pressure'), ['class' => 'form-label']) }}
                {{ Form::text('blood_pressure', null, ['class' => 'form-control', 'placeholder' => __('120/80')]) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="form-group">
                {{ Form::label('respiratory_rate', __('Respiratory Rate'), ['class' => 'form-label']) }}
                {{ Form::number('respiratory_rate', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="form-group">
                {{ Form::label('weight', __('Weight (kg)'), ['class' => 'form-label']) }}
                {{ Form::number('weight', null, ['class' => 'form-control', 'step' => '0.01']) }}
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="form-group">
                {{ Form::label('height', __('Height (cm)'), ['class' => 'form-label']) }}
                {{ Form::number('height', null, ['class' => 'form-control', 'step' => '0.01']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('clinical_observations', __('Clinical Observations'), ['class' => 'form-label']) }}
                {{ Form::textarea('clinical_observations', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('requested_exams', __('Requested Exams'), ['class' => 'form-label']) }}
                {{ Form::textarea('requested_exams', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('medical_certificate', __('Medical Certificate'), ['class' => 'form-label']) }}
                {{ Form::textarea('medical_certificate', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('sick_leave_start', __('Sick Leave Start'), ['class' => 'form-label']) }}
                {{ Form::date('sick_leave_start', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('sick_leave_end', __('Sick Leave End'), ['class' => 'form-label']) }}
                {{ Form::date('sick_leave_end', null, ['class' => 'form-control']) }}
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
