@extends('layouts.admin')

@section('page-title')
    {{ __('Medical Reports') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical.operations.index') }}">{{ __('Advanced Medical Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Reports') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Monitor care throughput, critical laboratory activity, surgical load, telecare and biomedical readiness.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Emergency waiting') }}</span><strong class="ux-kpi-value">{{ $kpis['emergency_waiting'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Critical lab orders') }}</span><strong class="ux-kpi-value">{{ $kpis['critical_lab_orders'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Planned surgeries') }}</span><strong class="ux-kpi-value">{{ $kpis['planned_surgeries'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Telemedicine open') }}</span><strong class="ux-kpi-value">{{ $kpis['telemedicine_open'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Biomed due') }}</span><strong class="ux-kpi-value">{{ $kpis['biomedical_due'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Medical revenue') }}</span><strong class="ux-kpi-value">{{ \Auth::user()->priceFormat($kpis['medical_revenue']) }}</strong></div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap gap-2">
            <a href="{{ route('medical.operations.laboratory') }}" class="btn btn-sm btn-outline-primary">{{ __('Open Laboratory Board') }}</a>
            <a href="{{ route('medical.operations.surgery') }}" class="btn btn-sm btn-outline-primary">{{ __('Open Surgery Board') }}</a>
            <a href="{{ route('medical.operations.biomedical') }}" class="btn btn-sm btn-outline-primary">{{ __('Open Biomedical Board') }}</a>
            <a href="{{ route('medical.operations.specialties') }}" class="btn btn-sm btn-outline-primary">{{ __('Open Specialties') }}</a>
            <a href="{{ route('medical.patient-portal') }}" class="btn btn-sm btn-primary">{{ __('Open Patient Portal') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Emergency Visits') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Patient') }}</th><th>{{ __('Triage') }}</th><th>{{ __('Complaint') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($emergencyVisits as $visit)
                            <tr>
                                <td>{{ optional($visit->patient)->first_name }} {{ optional($visit->patient)->last_name }}</td>
                                <td>{{ strtoupper($visit->triage_level) }}</td>
                                <td>{{ $visit->chief_complaint }}</td>
                                <td>{{ ucfirst($visit->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Lab Activity') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Patient') }}</th><th>{{ __('Panel') }}</th><th>{{ __('Critical') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($labOrders as $order)
                            <tr>
                                <td>{{ optional($order->patient)->first_name }} {{ optional($order->patient)->last_name }}</td>
                                <td>{{ $order->panel_name }}</td>
                                <td>{{ $order->critical_flag ? __('Yes') : __('No') }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Surgeries') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Patient') }}</th><th>{{ __('Procedure') }}</th><th>{{ __('Scheduled') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($surgeries as $procedure)
                            <tr>
                                <td>{{ optional($procedure->patient)->first_name }} {{ optional($procedure->patient)->last_name }}</td>
                                <td>{{ $procedure->procedure_name }}</td>
                                <td>{{ $procedure->scheduled_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $procedure->status)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>{{ __('Biomedical Assets') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Asset') }}</th><th>{{ __('Location') }}</th><th>{{ __('Calibration due') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($biomedicalAssets as $asset)
                            <tr>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->location ?: '-' }}</td>
                                <td>{{ $asset->calibration_due_date?->format('Y-m-d') ?: '-' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $asset->maintenance_status)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h5>{{ __('Medical Revenue Snapshot') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Invoice') }}</th><th>{{ __('Patient') }}</th><th>{{ __('Date') }}</th><th>{{ __('Patient Amount') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @foreach($medicalInvoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ optional($invoice->patient)->first_name }} {{ optional($invoice->patient)->last_name }}</td>
                                <td>{{ $invoice->invoice_date?->format('Y-m-d') }}</td>
                                <td>{{ \Auth::user()->priceFormat($invoice->patient_amount) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
