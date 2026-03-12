{{ Form::open(['route' => 'medical-invoices.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6"><label class="form-label">{{ __('Patient') }}</label><select name="patient_id" class="form-control" required><option value="">{{ __('Select patient') }}</option>@foreach($patients as $patient)<option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">{{ __('Status') }}</label>{{ Form::select('status', ['draft' => __('Draft'), 'unpaid' => __('Unpaid'), 'partial' => __('Partial'), 'paid' => __('Paid')], 'draft', ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2"><label class="form-label">{{ __('Consultation') }}</label><select name="consultation_id" class="form-control"><option value="">{{ __('Select consultation') }}</option>@foreach($consultations as $consultation)<option value="{{ $consultation->id }}">{{ optional($consultation->patient)->first_name }} {{ optional($consultation->patient)->last_name }} - {{ \Auth::user()->dateFormat($consultation->consultation_date) }}</option>@endforeach</select></div>
    <div class="col-md-6 mt-2"><label class="form-label">{{ __('Appointment') }}</label><select name="appointment_id" class="form-control"><option value="">{{ __('Select appointment') }}</option>@foreach($appointments as $appointment)<option value="{{ $appointment->id }}">{{ optional($appointment->patient)->first_name }} {{ optional($appointment->patient)->last_name }} - {{ \Auth::user()->dateFormat($appointment->start_at) }}</option>@endforeach</select></div>
    <div class="col-md-6 mt-2">{{ Form::label('invoice_date', __('Invoice Date'), ['class' => 'form-label']) }}{{ Form::date('invoice_date', now()->format('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}{{ Form::date('due_date', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-12 mt-2">{{ Form::label('insurer_name', __('Insurer / Mutual'), ['class' => 'form-label']) }}{{ Form::text('insurer_name', null, ['class' => 'form-control']) }}</div>
    @for($i = 0; $i < 3; $i++)
        <div class="col-md-4 mt-3"><label class="form-label">{{ __('Medical Service') }} {{ $i + 1 }}</label><select name="items[{{ $i }}][medical_service_id]" class="form-control"><option value="">{{ __('Select service') }}</option>@foreach($services as $service)<option value="{{ $service->id }}">{{ $service->name }}</option>@endforeach</select></div>
        <div class="col-md-4 mt-3">{{ Form::label("items[$i][description]", __('Description'), ['class' => 'form-label']) }}{{ Form::text("items[$i][description]", null, ['class' => 'form-control']) }}</div>
        <div class="col-md-2 mt-3">{{ Form::label("items[$i][quantity]", __('Qty'), ['class' => 'form-label']) }}{{ Form::number("items[$i][quantity]", 1, ['class' => 'form-control', 'step' => '0.01']) }}</div>
        <div class="col-md-2 mt-3">{{ Form::label("items[$i][unit_price]", __('Price'), ['class' => 'form-label']) }}{{ Form::number("items[$i][unit_price]", 0, ['class' => 'form-control', 'step' => '0.01']) }}</div>
        <div class="col-md-12 mt-2">{{ Form::label("items[$i][coverage_rate]", __('Coverage %'), ['class' => 'form-label']) }}{{ Form::number("items[$i][coverage_rate]", 0, ['class' => 'form-control', 'step' => '0.01']) }}</div>
    @endfor
    <div class="col-12 mt-3">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}
