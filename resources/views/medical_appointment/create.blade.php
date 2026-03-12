{{ Form::open(['route' => 'medical-appointments.store', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('patient_id', __('Patient'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="patient_id" class="form-control" required>
                    <option value="">{{ __('Select patient') }}</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('doctor_id', __('Doctor'), ['class' => 'form-label']) }}
                <select name="doctor_id" class="form-control">
                    <option value="">{{ __('Select doctor') }}</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('start_at', __('Start Date & Time'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::datetimeLocal('start_at', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('end_at', __('End Date & Time'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::datetimeLocal('end_at', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('room', __('Room'), ['class' => 'form-label']) }}
                {{ Form::text('room', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('specialty', __('Specialty'), ['class' => 'form-label']) }}
                {{ Form::text('specialty', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('appointment_type', __('Appointment Type'), ['class' => 'form-label']) }}
                {{ Form::text('appointment_type', null, ['class' => 'form-control', 'placeholder' => __('Consultation, follow-up, emergency...')]) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('queue_number', __('Queue Number'), ['class' => 'form-label']) }}
                {{ Form::number('queue_number', null, ['class' => 'form-control', 'min' => 1]) }}
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-center">
            <div class="form-check mt-4">
                {{ Form::checkbox('is_waiting_list', 1, false, ['class' => 'form-check-input', 'id' => 'is_waiting_list']) }}
                {{ Form::label('is_waiting_list', __('Waiting List'), ['class' => 'form-check-label']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('reminder_channel', __('Reminder Channel'), ['class' => 'form-label']) }}
                {{ Form::select('reminder_channel', ['email' => __('Email'), 'sms' => __('SMS'), 'whatsapp' => __('WhatsApp')], null, ['class' => 'form-control', 'placeholder' => __('None')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('reminder_at', __('Reminder Date & Time'), ['class' => 'form-label']) }}
                {{ Form::datetimeLocal('reminder_at', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
