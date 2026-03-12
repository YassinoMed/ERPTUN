@extends('layouts.admin')

@section('page-title')
    {{ __('Traceability Network') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Traceability Network') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Visualize the upstream and downstream network of each lot with events, checks and export evidence.') }}
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('agri.traceability.network') }}" class="row">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Lot') }}</label>
                    <select name="lot_id" class="form-control" onchange="this.form.submit()">
                        @foreach($lots as $lot)
                            <option value="{{ $lot->id }}" {{ $selectedLot && $selectedLot->id === $lot->id ? 'selected' : '' }}>
                                {{ $lot->code }} - {{ $lot->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($selectedLot)
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">{{ __('Upstream / Downstream Batches') }}</h5></div>
                    <div class="card-body">
                        <h6>{{ __('Transformations involving selected lot') }}</h6>
                        @forelse ($upstreamBatches as $batch)
                            <div class="border-bottom py-2">
                                <strong>{{ $batch->batch_number }}</strong>
                                <div class="small text-muted">
                                    {{ optional($batch->inputLot)->code ?: '-' }} -> {{ optional($batch->outputLot)->code ?: '-' }} /
                                    {{ $batch->process_step }} / {{ $batch->processed_at?->format('Y-m-d H:i') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No transformation chain found.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">{{ __('Export & Compliance') }}</h5></div>
                    <div class="card-body">
                        <h6>{{ __('Shipments') }}</h6>
                        @forelse ($shipments as $shipment)
                            <div class="border-bottom py-2">
                                <strong>{{ $shipment->shipment_ref }}</strong>
                                <div class="small text-muted">{{ $shipment->destination_country }} / {{ $shipment->status }} / {{ $shipment->departure_date?->format('Y-m-d') }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-3">{{ __('No shipment linked.') }}</p>
                        @endforelse

                        <h6 class="mt-3">{{ __('Compliance Checks') }}</h6>
                        @forelse ($checks as $check)
                            <div class="border-bottom py-2">
                                <strong>{{ $check->control_type }}</strong>
                                <div class="small text-muted">{{ $check->result }} / {{ $check->checked_at?->format('Y-m-d H:i') }} / {{ $check->certificate_ref ?: '-' }}</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No compliance checks linked.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('Trace Timeline') }}</h5></div>
            <div class="card-body">
                @forelse ($events as $event)
                    <div class="border-bottom py-2">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $event->step }}</strong>
                            <span>{{ $event->occurred_at?->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="small text-muted">{{ $event->location ?: '-' }} / {{ $event->actor ?: '-' }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('No trace events found for this lot.') }}</p>
                @endforelse
            </div>
        </div>
    @endif
@endsection
