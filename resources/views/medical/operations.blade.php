@extends('layouts.admin')

@section('page-title')
    {{ __('Advanced Medical Operations') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Advanced Medical Operations') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Supervise emergency intake, imaging, lab, surgery, nursing, biomed and telecare from one operational care board.') }}
@endsection

@section('action-button')
    <div class="d-flex gap-2">
        <a href="{{ route('medical.operations.reports') }}" class="btn btn-sm btn-primary">{{ __('Medical Reports') }}</a>
        <a href="{{ route('medical.patient-portal') }}" class="btn btn-sm btn-outline-primary">{{ __('Patient Portal') }}</a>
    </div>
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Emergency queue') }}</span>
            <strong class="ux-kpi-value">{{ $emergencyVisits->where('status', 'waiting')->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('patients awaiting triage or care') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Imaging backlog') }}</span>
            <strong class="ux-kpi-value">{{ $imagingOrders->whereIn('status', ['ordered', 'scheduled'])->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('orders pending completion') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Open lab orders') }}</span>
            <strong class="ux-kpi-value">{{ $labOrders->whereIn('status', ['ordered', 'collected'])->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('samples awaiting validation') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Telemedicine') }}</span>
            <strong class="ux-kpi-value">{{ $telemedicineSessions->whereIn('status', ['planned', 'in_progress'])->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('remote sessions scheduled') }}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Emergency Intake') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.emergency-visits.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Patient') }}</label>
                                <select name="patient_id" class="form-control" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Arrival') }}</label>
                                <input type="datetime-local" name="arrived_at" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Triage level') }}</label>
                                <select name="triage_level" class="form-control">
                                    <option value="red">{{ __('Red') }}</option>
                                    <option value="orange">{{ __('Orange') }}</option>
                                    <option value="yellow">{{ __('Yellow') }}</option>
                                    <option value="green">{{ __('Green') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Doctor') }}</label>
                                <input type="text" name="attending_doctor" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('Chief complaint') }}</label>
                                <input type="text" name="chief_complaint" class="form-control" required>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Register Emergency Visit') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Imaging Order') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.imaging-orders.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Patient') }}</label>
                                <select name="patient_id" class="form-control" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Consultation') }}</label>
                                <select name="consultation_id" class="form-control">
                                    <option value="">{{ __('Select consultation') }}</option>
                                    @foreach($consultations as $consultation)
                                        <option value="{{ $consultation->id }}">#{{ $consultation->id }} - {{ $consultation->consultation_date?->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Modality') }}</label>
                                <input type="text" name="modality" class="form-control" placeholder="XR / CT / MRI" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Body part') }}</label>
                                <input type="text" name="body_part" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Imaging Order') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Lab Order') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.lab-orders.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Patient') }}</label>
                                <select name="patient_id" class="form-control" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Ordered at') }}</label>
                                <input type="datetime-local" name="ordered_at" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Panel') }}</label>
                                <input type="text" name="panel_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Sample type') }}</label>
                                <input type="text" name="sample_type" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="ordered">{{ __('Ordered') }}</option>
                                    <option value="collected">{{ __('Collected') }}</option>
                                    <option value="validated">{{ __('Validated') }}</option>
                                    <option value="completed">{{ __('Completed') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="critical_flag" value="1" id="critical_flag">
                                    <label class="form-check-label" for="critical_flag">{{ __('Critical result expected') }}</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Lab Order') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Medical Specialty') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.specialties.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Code') }}</label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Department') }}</label>
                                <input type="text" name="department_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Head of specialty') }}</label>
                                <input type="text" name="head_name" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Specialty') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Nursing Care') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.nursing-cares.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Patient') }}</label>
                                <select name="patient_id" class="form-control" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Admission') }}</label>
                                <select name="hospital_admission_id" class="form-control">
                                    <option value="">{{ __('Select admission') }}</option>
                                    @foreach($admissions as $admission)
                                        <option value="{{ $admission->id }}">{{ $admission->admission_number ?? ('#' . $admission->id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Care type') }}</label>
                                <input type="text" name="care_type" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Scheduled at') }}</label>
                                <input type="datetime-local" name="scheduled_at" class="form-control" required>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Plan Nursing Care') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Telemedicine Session') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.telemedicine-sessions.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Patient') }}</label>
                                <select name="patient_id" class="form-control" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Appointment') }}</label>
                                <select name="appointment_id" class="form-control">
                                    <option value="">{{ __('Select appointment') }}</option>
                                    @foreach($appointments as $appointment)
                                        <option value="{{ $appointment->id }}">#{{ $appointment->id }} - {{ $appointment->start_at?->format('Y-m-d H:i') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Provider') }}</label>
                                <input type="text" name="provider_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Session link') }}</label>
                                <input type="url" name="session_link" class="form-control">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('Scheduled at') }}</label>
                                <input type="datetime-local" name="scheduled_at" class="form-control" required>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Telemedicine') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Surgical Procedure') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.surgical-procedures.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Patient') }}</label>
                                <select name="patient_id" class="form-control" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Admission') }}</label>
                                <select name="hospital_admission_id" class="form-control">
                                    <option value="">{{ __('Optional') }}</option>
                                    @foreach($admissions as $admission)
                                        <option value="{{ $admission->id }}">{{ $admission->admission_number ?? ('#' . $admission->id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Procedure') }}</label>
                                <input type="text" name="procedure_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Surgeon') }}</label>
                                <input type="text" name="surgeon_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Theatre') }}</label>
                                <input type="text" name="theatre_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Scheduled at') }}</label>
                                <input type="datetime-local" name="scheduled_at" class="form-control" required>
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Schedule Surgery') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Biomedical Asset') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('medical.operations.biomedical-assets.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Asset name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Asset code') }}</label>
                                <input type="text" name="asset_code" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Equipment type') }}</label>
                                <input type="text" name="equipment_type" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Serial number') }}</label>
                                <input type="text" name="serial_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Location') }}</label>
                                <input type="text" name="location" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Calibration due') }}</label>
                                <input type="date" name="calibration_due_date" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Asset') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Lab Orders') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Patient') }}</th><th>{{ __('Panel') }}</th><th>{{ __('Sample') }}</th><th>{{ __('Critical') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @forelse($labOrders as $order)
                            <tr>
                                <td>{{ optional($order->patient)->first_name }} {{ optional($order->patient)->last_name }}</td>
                                <td>{{ $order->panel_name }}</td>
                                <td>{{ $order->sample_type ?: '-' }}</td>
                                <td>{{ $order->critical_flag ? __('Yes') : __('No') }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No lab orders yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Surgical Board') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Patient') }}</th><th>{{ __('Procedure') }}</th><th>{{ __('Surgeon') }}</th><th>{{ __('Scheduled') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @forelse($surgicalProcedures as $procedure)
                            <tr>
                                <td>{{ optional($procedure->patient)->first_name }} {{ optional($procedure->patient)->last_name }}</td>
                                <td>{{ $procedure->procedure_name }}</td>
                                <td>{{ $procedure->surgeon_name ?: '-' }}</td>
                                <td>{{ $procedure->scheduled_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $procedure->status)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No surgical procedures yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Biomedical Assets') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Asset') }}</th><th>{{ __('Code') }}</th><th>{{ __('Type') }}</th><th>{{ __('Calibration due') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @forelse($biomedicalAssets as $asset)
                            <tr>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->asset_code }}</td>
                                <td>{{ $asset->equipment_type ?: '-' }}</td>
                                <td>{{ $asset->calibration_due_date?->format('Y-m-d') ?: '-' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $asset->maintenance_status)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No biomedical assets yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Medical Specialties') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Code') }}</th><th>{{ __('Department') }}</th><th>{{ __('Head') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @forelse($medicalSpecialties as $specialty)
                            <tr>
                                <td>{{ $specialty->name }}</td>
                                <td>{{ $specialty->code }}</td>
                                <td>{{ $specialty->department_name ?: '-' }}</td>
                                <td>{{ $specialty->head_name ?: '-' }}</td>
                                <td>{{ ucfirst($specialty->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No specialties yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
