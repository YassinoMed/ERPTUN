{{ Form::open(['route' => 'pharmacy-dispensations.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6"><label class="form-label">{{ __('Patient') }}</label><select name="patient_id" class="form-control" required><option value="">{{ __('Select patient') }}</option>@foreach($patients as $patient)<option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>@endforeach</select></div>
    <div class="col-md-6">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['dispensed' => __('Dispensed'), 'returned' => __('Returned')], 'dispensed', ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2"><label class="form-label">{{ __('Consultation') }}</label><select name="consultation_id" class="form-control"><option value="">{{ __('Select consultation') }}</option>@foreach($consultations as $consultation)<option value="{{ $consultation->id }}">{{ optional($consultation->patient)->first_name }} {{ optional($consultation->patient)->last_name }} - {{ \Auth::user()->dateFormat($consultation->consultation_date) }}</option>@endforeach</select></div>
    <div class="col-md-6 mt-2"><label class="form-label">{{ __('Prescription') }}</label><select name="prescription_id" class="form-control"><option value="">{{ __('Select prescription') }}</option>@foreach($prescriptions as $prescription)<option value="{{ $prescription->id }}">{{ $prescription->medication_name }}</option>@endforeach</select></div>
    <div class="col-md-12 mt-2">{{ Form::label('dispensed_at', __('Dispensed At'), ['class' => 'form-label']) }}{{ Form::datetimeLocal('dispensed_at', now()->format('Y-m-d\TH:i'), ['class' => 'form-control']) }}</div>
    @for($i = 0; $i < 3; $i++)
        <div class="col-md-4 mt-3"><label class="form-label">{{ __('Medication') }} {{ $i + 1 }}</label><select name="items[{{ $i }}][pharmacy_medication_id]" class="form-control"><option value="">{{ __('Select medication') }}</option>@foreach($medications as $medication)<option value="{{ $medication->id }}">{{ $medication->name }}</option>@endforeach</select></div>
        <div class="col-md-2 mt-3">{{ Form::label("items[$i][quantity]", __('Qty'), ['class' => 'form-label']) }}{{ Form::number("items[$i][quantity]", 1, ['class' => 'form-control', 'step' => '0.01']) }}</div>
        <div class="col-md-2 mt-3">{{ Form::label("items[$i][dosage]", __('Dosage'), ['class' => 'form-label']) }}{{ Form::text("items[$i][dosage]", null, ['class' => 'form-control']) }}</div>
        <div class="col-md-2 mt-3">{{ Form::label("items[$i][frequency]", __('Frequency'), ['class' => 'form-label']) }}{{ Form::text("items[$i][frequency]", null, ['class' => 'form-control']) }}</div>
        <div class="col-md-2 mt-3">{{ Form::label("items[$i][duration]", __('Duration'), ['class' => 'form-label']) }}{{ Form::text("items[$i][duration]", null, ['class' => 'form-control']) }}</div>
    @endfor
    <div class="col-12 mt-3">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
