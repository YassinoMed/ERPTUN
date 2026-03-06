@extends('layouts.admin')

@section('page-title')
    {{ __('Yield Management') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Yield Management') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Pricing Rule') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.yield.rules.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Room Type') }}</label>
                            <select name="room_type_id" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Occupancy Threshold') }}</label>
                            <input type="number" step="0.01" name="occupancy_threshold" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Min Rate') }}</label>
                            <input type="number" step="0.01" name="min_rate" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Max Rate') }}</label>
                            <input type="number" step="0.01" name="max_rate" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Margin') }}</label>
                            <input type="number" step="0.01" name="margin" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Rule') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Demand Forecast') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.yield.forecasts.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Room Type') }}</label>
                            <select name="room_type_id" class="form-control" required>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Date') }}</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Demand Score') }}</label>
                            <input type="number" step="0.01" name="demand_score" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Occupancy Rate') }}</label>
                            <input type="number" step="0.01" name="occupancy_rate" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Seasonal Factor') }}</label>
                            <input type="number" step="0.01" name="seasonal_factor" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Event Factor') }}</label>
                            <input type="number" step="0.01" name="event_factor" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Forecast') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>{{ __('Price Recommendations') }}</h5>
                    <form action="{{ route('hotel.yield.generate') }}" method="post">
                        @csrf
                        <div class="d-flex gap-2">
                            <select name="room_type_id" class="form-control">
                                <option value="">{{ __('All Room Types') }}</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date" class="form-control">
                            <button class="btn btn-primary">{{ __('Generate') }}</button>
                        </div>
                    </form>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Room Type') }}</th>
                                <th>{{ __('Rate Plan') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Recommended') }}</th>
                                <th>{{ __('Reason') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendations as $recommendation)
                                <tr>
                                    <td>{{ $recommendation->roomType?->name }}</td>
                                    <td>{{ $recommendation->ratePlan?->name }}</td>
                                    <td>{{ $recommendation->date?->format('Y-m-d') }}</td>
                                    <td>{{ $recommendation->recommended_rate }}</td>
                                    <td>{{ $recommendation->reason }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Pricing Rules') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Room Type') }}</th>
                                <th>{{ __('Min') }}</th>
                                <th>{{ __('Max') }}</th>
                                <th>{{ __('Margin') }}</th>
                                <th>{{ __('Occupancy') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $rule)
                                <tr>
                                    <td>{{ $rule->name }}</td>
                                    <td>{{ $rule->roomType?->name ?? __('All') }}</td>
                                    <td>{{ $rule->min_rate }}</td>
                                    <td>{{ $rule->max_rate }}</td>
                                    <td>{{ $rule->margin }}</td>
                                    <td>{{ $rule->occupancy_threshold }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
