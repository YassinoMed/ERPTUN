{{ Form::open(['route' => 'hospital-admissions.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6"><label class="form-label">{{ __('Patient') }}</label><select name="patient_id" class="form-control" required><option value="">{{ __('Select patient') }}</option>@foreach($patients as $patient)<option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">{{ __('Doctor') }}</label><select name="attending_doctor_id" class="form-control"><option value="">{{ __('Select doctor') }}</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ $doctor->name }}</option>@endforeach</select></div>
    <div class="col-md-6 mt-2"><label class="form-label">{{ __('Room') }}</label><select name="room_id" class="form-control"><option value="">{{ __('Select room') }}</option>@foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->name }}</option>@endforeach</select></div>
    <div class="col-md-6 mt-2"><label class="form-label">{{ __('Bed') }}</label><select name="bed_id" class="form-control"><option value="">{{ __('Select bed') }}</option>@foreach($beds as $bed)<option value="{{ $bed->id }}">{{ optional($bed->room)->name }} / {{ $bed->bed_number }}</option>@endforeach</select></div>
    <div class="col-md-6 mt-2">{{ Form::label('admission_date', __('Admission Date'), ['class' => 'form-label']) }}{{ Form::datetimeLocal('admission_date', now()->format('Y-m-d\TH:i'), ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['admitted' => __('Admitted'), 'observation' => __('Observation'), 'discharged' => __('Discharged')], 'admitted', ['class' => 'form-control']) }}</div>
    <div class="col-md-12 mt-2">{{ Form::label('reason', __('Reason'), ['class' => 'form-label']) }}{{ Form::text('reason', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-12 mt-2">{{ Form::label('diagnosis', __('Diagnosis'), ['class' => 'form-label']) }}{{ Form::textarea('diagnosis', null, ['class' => 'form-control', 'rows' => 2]) }}</div>
    <div class="col-md-12 mt-2">{{ Form::label('care_plan', __('Care Plan'), ['class' => 'form-label']) }}{{ Form::textarea('care_plan', null, ['class' => 'form-control', 'rows' => 2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
