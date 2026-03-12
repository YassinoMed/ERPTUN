@extends('layouts.admin')

@section('page-title')
    {{ __('Agri Operations') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Agri Operations') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Run weighing, cold chain, transformation and export execution from a single agro operations desk.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Weighings') }}</span>
            <strong class="ux-kpi-value">{{ $weighings->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('recent lot entries') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Cold storage') }}</span>
            <strong class="ux-kpi-value">{{ $coldStorages->where('status', 'stored')->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('lots currently stored') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('Export shipments') }}</span>
            <strong class="ux-kpi-value">{{ $exportShipments->whereIn('status', ['ready', 'shipped'])->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('ready or in transit') }}</span>
        </div>
        <div class="ux-kpi-card">
            <span class="ux-kpi-label">{{ __('FEFO alerts') }}</span>
            <strong class="ux-kpi-value">{{ $fefoAlerts->count() }}</strong>
            <span class="ux-kpi-meta">{{ __('lots approaching expiry') }}</span>
        </div>
    </div>
    <div class="mb-3">
        <a href="{{ route('agri.operations.fefo') }}" class="btn btn-outline-danger">{{ __('Open FEFO Board') }}</a>
        <a href="{{ route('agri.reports.index') }}" class="btn btn-outline-primary">{{ __('Open Agri Reports') }}</a>
        <a href="{{ route('agri.planning.dashboard') }}" class="btn btn-outline-dark">{{ __('Agriculture Dashboard') }}</a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5>{{ __('Weighing Ticket') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('agri.operations.weighings.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot') }}</label>
                            <select name="lot_id" class="form-control">
                                <option value="">{{ __('Select lot') }}</option>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Cooperative') }}</label>
                            <select name="cooperative_id" class="form-control">
                                <option value="">{{ __('Select cooperative') }}</option>
                                @foreach($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}">{{ $cooperative->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Producer') }}</label>
                            <input type="text" name="producer_name" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Gross') }}</label>
                                <input type="number" step="0.001" name="gross_weight" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Tare') }}</label>
                                <input type="number" step="0.001" name="tare_weight" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Moisture %') }}</label>
                                <input type="number" step="0.01" name="moisture_percent" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Quality grade') }}</label>
                                <input type="text" name="quality_grade" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Weighing date') }}</label>
                            <input type="date" name="weighing_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <button class="btn btn-primary">{{ __('Record Weighing') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Cold Storage Entry') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('agri.operations.cold-storage.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot') }}</label>
                            <select name="lot_id" class="form-control">
                                <option value="">{{ __('Select lot') }}</option>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Facility') }}</label>
                            <input type="text" name="facility_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Chamber') }}</label>
                            <input type="text" name="chamber_name" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label class="form-label">{{ __('Temp') }}</label>
                                <input type="number" step="0.01" name="temperature" class="form-control">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label class="form-label">{{ __('Humidity') }}</label>
                                <input type="number" step="0.01" name="humidity" class="form-control">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label class="form-label">{{ __('Qty') }}</label>
                                <input type="number" step="0.001" name="quantity" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Entry date') }}</label>
                                <input type="date" name="entry_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Expiry') }}</label>
                                <input type="date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Storage Record') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Export Shipment') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('agri.operations.export-shipments.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot') }}</label>
                            <select name="lot_id" class="form-control">
                                <option value="">{{ __('Select lot') }}</option>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Shipment ref') }}</label>
                            <input type="text" name="shipment_ref" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Customer') }}</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Destination country') }}</label>
                            <input type="text" name="destination_country" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Container') }}</label>
                                <input type="text" name="container_no" class="form-control">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Incoterm') }}</label>
                                <input type="text" name="incoterm" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Shipped qty') }}</label>
                                <input type="number" step="0.001" name="shipped_quantity" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Departure') }}</label>
                                <input type="date" name="departure_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Shipment') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Transformation Batch') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('agri.operations.transformation-batches.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Input lot') }}</label>
                            <select name="input_lot_id" class="form-control" required>
                                <option value="">{{ __('Select lot') }}</option>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Output lot code') }}</label>
                                <input type="text" name="output_lot_code" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Output lot name') }}</label>
                                <input type="text" name="output_lot_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Process step') }}</label>
                            <input type="text" name="process_step" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Facility') }}</label>
                            <input type="text" name="facility_name" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label class="form-label">{{ __('Input qty') }}</label>
                                <input type="number" step="0.001" name="input_quantity" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label class="form-label">{{ __('Output qty') }}</label>
                                <input type="number" step="0.001" name="output_quantity" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label class="form-label">{{ __('Waste qty') }}</label>
                                <input type="number" step="0.001" name="waste_quantity" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Processed at') }}</label>
                                <input type="datetime-local" name="processed_at" class="form-control" required value="{{ now()->format('Y-m-d\\TH:i') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Expiry date') }}</label>
                                <input type="date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ __('Record Transformation') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Compliance Check') }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('agri.operations.compliance-checks.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot') }}</label>
                            <select name="lot_id" class="form-control" required>
                                <option value="">{{ __('Select lot') }}</option>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Control type') }}</label>
                                <input type="text" name="control_type" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Result') }}</label>
                                <select name="result" class="form-control" required>
                                    <option value="pass">{{ __('Pass') }}</option>
                                    <option value="warning">{{ __('Warning') }}</option>
                                    <option value="fail">{{ __('Fail') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Measured value') }}</label>
                                <input type="text" name="measured_value" class="form-control">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">{{ __('Threshold value') }}</label>
                                <input type="text" name="threshold_value" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Checked at') }}</label>
                            <input type="datetime-local" name="checked_at" class="form-control" required value="{{ now()->format('Y-m-d\\TH:i') }}">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Compliance Check') }}</button>
                        <a href="{{ route('agri.reports.index') }}" class="btn btn-outline-primary">{{ __('Reports') }}</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5>{{ __('Recent Weighings') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Lot') }}</th><th>{{ __('Producer') }}</th><th>{{ __('Net') }}</th><th>{{ __('Grade') }}</th></tr></thead>
                        <tbody>
                        @forelse($weighings as $weighing)
                            <tr>
                                <td>{{ $weighing->weighing_date?->format('Y-m-d') }}</td>
                                <td>{{ optional($weighing->lot)->code ?? '-' }}</td>
                                <td>{{ $weighing->producer_name ?: optional($weighing->cooperative)->name ?: '-' }}</td>
                                <td>{{ $weighing->net_weight }}</td>
                                <td>{{ $weighing->quality_grade ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No weighings recorded yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Transformation Yield') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Batch') }}</th><th>{{ __('Input lot') }}</th><th>{{ __('Output lot') }}</th><th>{{ __('Yield') }}</th></tr></thead>
                        <tbody>
                        @forelse($transformationBatches as $batch)
                            <tr>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ optional($batch->inputLot)->code ?? '-' }}</td>
                                <td>{{ optional($batch->outputLot)->code ?? '-' }}</td>
                                <td>{{ $batch->output_quantity }} / {{ $batch->input_quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">{{ __('No transformation batches yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Cold Chain Monitoring') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Lot') }}</th><th>{{ __('Facility') }}</th><th>{{ __('Temp') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @forelse($coldStorages as $record)
                            <tr>
                                <td>{{ optional($record->lot)->code ?? '-' }}</td>
                                <td>{{ $record->facility_name }} / {{ $record->chamber_name ?: '-' }}</td>
                                <td>{{ $record->temperature ?? '-' }}</td>
                                <td>{{ $record->quantity }}</td>
                                <td>{{ ucfirst($record->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No cold storage records yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Compliance & FEFO') }}</h5></div>
                <div class="card-body">
                    @forelse($fefoAlerts as $alert)
                        <div class="alert alert-warning py-2">
                            {{ optional($alert->lot)->code ?? '-' }}:
                            {{ __('expiry on') }} {{ optional($alert->expiry_date)->format('Y-m-d') }}
                        </div>
                    @empty
                        <p class="text-muted">{{ __('No FEFO alert in the next 14 days.') }}</p>
                    @endforelse
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>{{ __('Lot') }}</th><th>{{ __('Control') }}</th><th>{{ __('Result') }}</th><th>{{ __('Checked') }}</th></tr></thead>
                            <tbody>
                            @forelse($complianceChecks as $check)
                                <tr>
                                    <td>{{ optional($check->lot)->code ?? '-' }}</td>
                                    <td>{{ $check->control_type }}</td>
                                    <td>{{ ucfirst($check->result) }}</td>
                                    <td>{{ $check->checked_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">{{ __('No compliance checks recorded yet.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Export Desk') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead><tr><th>{{ __('Shipment') }}</th><th>{{ __('Country') }}</th><th>{{ __('Lot') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                        @forelse($exportShipments as $shipment)
                            <tr>
                                <td>{{ $shipment->shipment_ref }}</td>
                                <td>{{ $shipment->destination_country }}</td>
                                <td>{{ optional($shipment->lot)->code ?? '-' }}</td>
                                <td>{{ $shipment->shipped_quantity }}</td>
                                <td>{{ ucfirst($shipment->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">{{ __('No export shipments yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
