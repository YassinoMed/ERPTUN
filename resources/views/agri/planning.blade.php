@extends('layouts.admin')

@section('page-title')
    {{ __('Crop Planning') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Crop Planning') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Parcel') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.planning.parcels.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Area') }}</label>
                            <input type="number" step="0.01" name="area" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Area Unit') }}</label>
                            <input type="text" name="area_unit" class="form-control" value="ha">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Soil Type') }}</label>
                            <input type="text" name="soil_type" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Location') }}</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Parcel') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Crop Plan') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.planning.plans.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Parcel') }}</label>
                            <select name="parcel_id" class="form-control" required>
                                @foreach($parcels as $parcel)
                                    <option value="{{ $parcel->id }}">{{ $parcel->code }} - {{ $parcel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Crop Name') }}</label>
                            <input type="text" name="crop_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Variety') }}</label>
                            <input type="text" name="variety" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Expected Yield') }}</label>
                            <input type="number" step="0.001" name="expected_yield" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Yield Unit') }}</label>
                            <input type="text" name="yield_unit" class="form-control" value="kg">
                        </div>
                        <button class="btn btn-primary">{{ __('Create Plan') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Rotation Rule') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.planning.rotation.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Crop Name') }}</label>
                            <input type="text" name="crop_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Follow Crop') }}</label>
                            <input type="text" name="follow_crop_name" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Min Gap Days') }}</label>
                            <input type="number" name="min_gap_days" class="form-control" value="0">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Recommendation') }}</label>
                            <textarea name="recommendation" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Rule') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Weather Alert') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.planning.alerts.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Parcel') }}</label>
                            <select name="parcel_id" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach($parcels as $parcel)
                                    <option value="{{ $parcel->id }}">{{ $parcel->code }} - {{ $parcel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Alert Type') }}</label>
                            <input type="text" name="alert_type" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Severity') }}</label>
                            <select name="severity" class="form-control">
                                <option value="low">{{ __('Low') }}</option>
                                <option value="medium" selected>{{ __('Medium') }}</option>
                                <option value="high">{{ __('High') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Alert Date') }}</label>
                            <input type="datetime-local" name="alert_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Message') }}</label>
                            <textarea name="message" class="form-control" rows="2" required></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Alert') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Parcels') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Area') }}</th>
                                <th>{{ __('Soil') }}</th>
                                <th>{{ __('Location') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parcels as $parcel)
                                <tr>
                                    <td>{{ $parcel->code }}</td>
                                    <td>{{ $parcel->name }}</td>
                                    <td>{{ $parcel->area }} {{ $parcel->area_unit }}</td>
                                    <td>{{ $parcel->soil_type }}</td>
                                    <td>{{ $parcel->location }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Crop Plans') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Parcel') }}</th>
                                <th>{{ __('Crop') }}</th>
                                <th>{{ __('Dates') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Yield') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>{{ $plan->parcel_id }}</td>
                                    <td>{{ $plan->crop_name }}</td>
                                    <td>{{ $plan->start_date?->format('Y-m-d') }} → {{ $plan->end_date?->format('Y-m-d') }}</td>
                                    <td>{{ $plan->status }}</td>
                                    <td>{{ $plan->expected_yield }} {{ $plan->yield_unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Rotation Recommendations') }}</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('agri.planning.index') }}" class="row mb-3">
                        <div class="col-md-6">
                            <select name="parcel_id" class="form-control" onchange="this.form.submit()">
                                @foreach($parcels as $parcel)
                                    <option value="{{ $parcel->id }}" {{ $selectedParcel && $selectedParcel->id === $parcel->id ? 'selected' : '' }}>
                                        {{ $parcel->code }} - {{ $parcel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Current Crop') }}</th>
                                    <th>{{ __('Next Crop') }}</th>
                                    <th>{{ __('Gap Days') }}</th>
                                    <th>{{ __('Recommendation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rotationRecommendations as $recommendation)
                                    <tr>
                                        <td>{{ $recommendation['crop_name'] }}</td>
                                        <td>{{ $recommendation['follow_crop_name'] }}</td>
                                        <td>{{ $recommendation['min_gap_days'] }}</td>
                                        <td>{{ $recommendation['recommendation'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Weather Alerts') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Parcel') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Severity') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Message') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weatherAlerts as $alert)
                                <tr>
                                    <td>{{ $alert->parcel_id ?? __('All') }}</td>
                                    <td>{{ $alert->alert_type }}</td>
                                    <td>{{ $alert->severity }}</td>
                                    <td>{{ $alert->alert_date?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $alert->message }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
