@extends('layouts.admin')

@section('page-title')
    {{ __('Patient Portal') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical.operations.index') }}">{{ __('Advanced Medical Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Patient Portal') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Review appointments, lab flow, billing, telemedicine and secure patient communication from a single portal view.') }}
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('medical.patient-portal') }}" class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">{{ __('Patient') }}</label>
                    <select name="patient_id" class="form-control">
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(optional($selectedPatient)->id === $patient->id)>{{ $patient->first_name }} {{ $patient->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">{{ __('Open Portal View') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedPatient)
        <div class="ux-kpi-grid mb-4">
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Appointments') }}</span><strong class="ux-kpi-value">{{ $appointments->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Lab orders') }}</span><strong class="ux-kpi-value">{{ $labOrders->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Invoices') }}</span><strong class="ux-kpi-value">{{ $medicalInvoices->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Telemedicine') }}</span><strong class="ux-kpi-value">{{ $telemedicineSessions->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Admissions') }}</span><strong class="ux-kpi-value">{{ $admissions->count() }}</strong></div>
            <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Nursing care') }}</span><strong class="ux-kpi-value">{{ $nursingCares->count() }}</strong></div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Clinical Summary') }}</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><strong>{{ __('Patient') }}:</strong> {{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}</div>
                            <div class="col-md-6"><strong>{{ __('Phone') }}:</strong> {{ $selectedPatient->contact ?? $selectedPatient->phone ?? '-' }}</div>
                            <div class="col-md-6"><strong>{{ __('Emergency visits') }}:</strong> {{ $emergencyVisits->count() }}</div>
                            <div class="col-md-6"><strong>{{ __('Outstanding due') }}:</strong> {{ \Auth::user()->priceFormat($medicalInvoices->sum(fn($invoice) => $invoice->dueAmount())) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5>{{ __('Portal Message') }}</h5></div>
                    <div class="card-body">
                        <form action="{{ route('medical.patient-portal.messages.store', $selectedPatient->id) }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('Subject') }}</label>
                                <input type="text" name="subject" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Message') }}</label>
                                <textarea name="message" class="form-control" rows="4" required></textarea>
                            </div>
                            <button class="btn btn-primary">{{ __('Send Message') }}</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5>{{ __('Portal Messages') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('When') }}</th><th>{{ __('Direction') }}</th><th>{{ __('Subject') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @foreach($portalMessages as $message)
                                <tr>
                                    <td>{{ $message->sent_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ ucfirst($message->direction) }}</td>
                                    <td>{{ $message->subject }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ ucfirst($message->status) }}</span>
                                            <form action="{{ route('medical.patient-portal.messages.status', $message->id) }}" method="post" class="d-flex gap-2">
                                                @csrf
                                                <select name="status" class="form-control form-control-sm">
                                                    <option value="sent" @selected($message->status === 'sent')>{{ __('Sent') }}</option>
                                                    <option value="read" @selected($message->status === 'read')>{{ __('Read') }}</option>
                                                    <option value="closed" @selected($message->status === 'closed')>{{ __('Closed') }}</option>
                                                </select>
                                                <button class="btn btn-sm btn-outline-primary">{{ __('Save') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5>{{ __('Appointments & Telemedicine') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Type') }}</th><th>{{ __('Status') }}</th><th>{{ __('Link') }}</th></tr></thead>
                            <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->start_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $appointment->appointment_type ?: __('Appointment') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $appointment->status ?? 'scheduled')) }}</td>
                                    <td>-</td>
                                </tr>
                            @endforeach
                            @foreach($telemedicineSessions as $session)
                                <tr>
                                    <td>{{ $session->scheduled_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ __('Telemedicine') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $session->status)) }}</td>
                                    <td>
                                        @if($session->session_link)
                                            <a href="{{ $session->session_link }}" target="_blank">{{ __('Open') }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5>{{ __('Laboratory & Surgery') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Type') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Status') }}</th><th>{{ __('Date') }}</th></tr></thead>
                            <tbody>
                            @foreach($labOrders as $order)
                                <tr>
                                    <td>{{ __('Lab') }}</td>
                                    <td>{{ $order->panel_name }}</td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                    <td>{{ $order->ordered_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                            @foreach($labResults as $result)
                                <tr>
                                    <td>{{ __('Lab Result') }}</td>
                                    <td>{{ $result->test_name }}</td>
                                    <td>{{ __('Validated') }}</td>
                                    <td>{{ $result->result_date ? \Auth::user()->dateFormat($result->result_date) : '-' }}</td>
                                </tr>
                            @endforeach
                            @foreach($surgicalProcedures as $procedure)
                                <tr>
                                    <td>{{ __('Surgery') }}</td>
                                    <td>{{ $procedure->procedure_name }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $procedure->status)) }}</td>
                                    <td>{{ $procedure->scheduled_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5>{{ __('Admissions & Nursing') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Type') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Status') }}</th><th>{{ __('Date') }}</th></tr></thead>
                            <tbody>
                            @foreach($admissions as $admission)
                                <tr>
                                    <td>{{ __('Admission') }}</td>
                                    <td>{{ $admission->admission_number ?? ('#' . $admission->id) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $admission->status ?? 'active')) }}</td>
                                    <td>{{ $admission->admission_date?->format('Y-m-d') ?: '-' }}</td>
                                </tr>
                            @endforeach
                            @foreach($nursingCares as $care)
                                <tr>
                                    <td>{{ __('Nursing') }}</td>
                                    <td>{{ $care->care_type }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $care->status)) }}</td>
                                    <td>{{ $care->scheduled_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                            @foreach($emergencyVisits as $visit)
                                <tr>
                                    <td>{{ __('Emergency') }}</td>
                                    <td>{{ strtoupper($visit->triage_level) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $visit->status)) }}</td>
                                    <td>{{ $visit->arrived_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5>{{ __('Billing') }}</h5></div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Invoice') }}</th><th>{{ __('Date') }}</th><th>{{ __('Patient due') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                            @foreach($medicalInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->invoice_date?->format('Y-m-d') }}</td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->dueAmount()) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
