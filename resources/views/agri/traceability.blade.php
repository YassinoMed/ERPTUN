@extends('layouts.admin')

@section('page-title')
    {{ __('Traceability') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Traceability') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track upstream origins, downstream transformations and export proofs for each agricultural lot.') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Lot') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.traceability.lots.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Crop Type') }}</label>
                            <input type="text" name="crop_type" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Source Reference') }}</label>
                            <input type="text" name="source_reference" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Parcel Origin') }}</label>
                            <input type="text" name="parcel_origin" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Harvest Date') }}</label>
                            <input type="date" name="harvest_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Expiry Date') }}</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Quantity') }}</label>
                            <input type="number" step="0.001" name="quantity" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Unit') }}</label>
                            <input type="text" name="unit" class="form-control" value="kg">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Lot') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Trace Event') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.traceability.events.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot') }}</label>
                            <select name="lot_id" class="form-control" required>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Step') }}</label>
                            <input type="text" name="step" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Location') }}</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Actor') }}</label>
                            <input type="text" name="actor" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Occurred At') }}</label>
                            <input type="datetime-local" name="occurred_at" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Add Event') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Certificate') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.traceability.certificates.issue') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot') }}</label>
                            <select name="lot_id" class="form-control" required>
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}">{{ $lot->code }} - {{ $lot->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Issue Certificate') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('agri.traceability.network') }}" class="btn btn-outline-primary">{{ __('Open Network View') }}</a>
                <a href="{{ route('agri.reports.index') }}" class="btn btn-outline-secondary">{{ __('Open Agro Reports') }}</a>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Lots') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Crop') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lots as $lot)
                                <tr>
                                    <td>{{ $lot->code }}</td>
                                    <td>{{ $lot->name }}</td>
                                    <td>{{ $lot->crop_type }}</td>
                                    <td>{{ $lot->quantity }} {{ $lot->unit }}</td>
                                    <td>{{ $lot->status }} / {{ $lot->quality_status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Events') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Lot') }}</th>
                                <th>{{ __('Step') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Occurred') }}</th>
                                <th>{{ __('Hash') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->lot_id }}</td>
                                    <td>{{ $event->step }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ $event->occurred_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $event->current_hash }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Certificates') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Certificate') }}</th>
                                <th>{{ __('Lot') }}</th>
                                <th>{{ __('Issued At') }}</th>
                                <th>{{ __('Hash') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->certificate_number }}</td>
                                    <td>{{ $certificate->lot_id }}</td>
                                    <td>{{ $certificate->issued_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $certificate->verification_hash }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Trace Chain') }}</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('agri.traceability.index') }}" class="row mb-3">
                        <div class="col-md-6">
                            <select name="lot_id" class="form-control" onchange="this.form.submit()">
                                @foreach($lots as $lot)
                                    <option value="{{ $lot->id }}" {{ $selectedLot && $selectedLot->id === $lot->id ? 'selected' : '' }}>
                                        {{ $lot->code }} - {{ $lot->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Step') }}</th>
                                    <th>{{ __('Occurred') }}</th>
                                    <th>{{ __('Prev Hash') }}</th>
                                    <th>{{ __('Hash') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($traceChain as $event)
                                    <tr>
                                        <td>{{ $event->step }}</td>
                                        <td>{{ $event->occurred_at?->format('Y-m-d H:i') }}</td>
                                        <td>{{ $event->previous_hash }}</td>
                                        <td>{{ $event->current_hash }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($selectedLot)
                        <div class="mt-4">
                            <h6>{{ __('Selected Lot Context') }}</h6>
                            <div class="small text-muted mb-2">
                                {{ __('Source') }}: {{ $selectedLot->source_reference ?: '-' }} /
                                {{ __('Parcel') }}: {{ $selectedLot->parcel_origin ?: '-' }} /
                                {{ __('Expiry') }}: {{ optional($selectedLot->expiry_date)->format('Y-m-d') ?: '-' }}
                            </div>
                            @foreach($coldAlerts as $alert)
                                <div class="alert alert-warning py-2">
                                    {{ $alert['message'] }} {{ optional($alert['expiry_date'])->format('Y-m-d') }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5>{{ __('Upstream / Downstream') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('Upstream Batches') }}</h6>
                            @forelse($upstreamBatches as $batch)
                                <div class="border-bottom py-2">
                                    <strong>{{ $batch->batch_number }}</strong>
                                    <div class="small text-muted">{{ optional($batch->inputLot)->code ?? '-' }} -> {{ optional($batch->outputLot)->code ?? '-' }}</div>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('No upstream transformation.') }}</p>
                            @endforelse
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('Downstream Batches') }}</h6>
                            @forelse($downstreamBatches as $batch)
                                <div class="border-bottom py-2">
                                    <strong>{{ $batch->batch_number }}</strong>
                                    <div class="small text-muted">{{ optional($batch->inputLot)->code ?? '-' }} -> {{ optional($batch->outputLot)->code ?? '-' }}</div>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('No downstream transformation.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5>{{ __('Compliance & Export') }}</h5></div>
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Control') }}</th>
                                    <th>{{ __('Result') }}</th>
                                    <th>{{ __('Checked At') }}</th>
                                    <th>{{ __('Certificate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complianceChecks as $check)
                                    <tr>
                                        <td>{{ $check->control_type }}</td>
                                        <td>{{ ucfirst($check->result) }}</td>
                                        <td>{{ $check->checked_at?->format('Y-m-d H:i') }}</td>
                                        <td>{{ $check->certificate_ref ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">{{ __('No compliance checks for this lot.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Shipment') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shipments as $shipment)
                                    <tr>
                                        <td>{{ $shipment->shipment_ref }}</td>
                                        <td>{{ $shipment->destination_country }}</td>
                                        <td>{{ $shipment->shipped_quantity }}</td>
                                        <td>{{ ucfirst($shipment->status) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">{{ __('No export movement for this lot.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
