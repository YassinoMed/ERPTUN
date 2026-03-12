{{ Form::model($appointment, ['route' => ['medical-appointments.update', $appointment->id], 'method' => 'put', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('patient_id', __('Patient'), ['class' => 'form-label']) }}<x-required></x-required>
                <select name="patient_id" class="form-control" required>
                    <option value="">{{ __('Select patient') }}</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>{{ $patient->first_name }} {{ $patient->last_name }}</option>
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
                        <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('start_at', __('Start Date & Time'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::datetimeLocal('start_at', $appointment->start_at ? \Carbon\Carbon::parse($appointment->start_at)->format('Y-m-d\TH:i') : null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('end_at', __('End Date & Time'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::datetimeLocal('end_at', $appointment->end_at ? \Carbon\Carbon::parse($appointment->end_at)->format('Y-m-d\TH:i') : null, ['class' => 'form-control', 'required' => 'required']) }}
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
                {{ Form::text('appointment_type', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('queue_number', __('Queue Number'), ['class' => 'form-label']) }}
                {{ Form::number('queue_number', null, ['class' => 'form-control', 'min' => 1]) }}
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <div class="form-check mt-4">
                {{ Form::checkbox('is_waiting_list', 1, $appointment->is_waiting_list, ['class' => 'form-check-input', 'id' => 'is_waiting_list']) }}
                {{ Form::label('is_waiting_list', __('Waiting List'), ['class' => 'form-check-label']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                {{ Form::select('status', ['scheduled' => __('Scheduled'), 'confirmed' => __('Confirmed'), 'in_progress' => __('Checked In'), 'completed' => __('Completed'), 'canceled' => __('Canceled')], $appointment->status, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('reminder_channel', __('Reminder Channel'), ['class' => 'form-label']) }}
                {{ Form::select('reminder_channel', ['email' => __('Email'), 'sms' => __('SMS'), 'whatsapp' => __('WhatsApp')], $appointment->reminder_channel, ['class' => 'form-control', 'placeholder' => __('None')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('reminder_at', __('Reminder Date & Time'), ['class' => 'form-label']) }}
                {{ Form::datetimeLocal('reminder_at', $appointment->reminder_at ? \Carbon\Carbon::parse($appointment->reminder_at)->format('Y-m-d\TH:i') : null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('cancel_reason', __('Cancel Reason'), ['class' => 'form-label']) }}
                {{ Form::text('cancel_reason', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary">
</div>
{{ Form::close() }}
