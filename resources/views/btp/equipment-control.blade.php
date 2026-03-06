@extends('layouts.admin')

@section('page-title')
    {{ __('BTP Equipment Control') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('BTP Equipment Control') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Equipment') }}</h6>
                            <h3 class="mb-0">{{ $totalEquipment }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Available') }}</h6>
                            <h3 class="mb-0">{{ $availableEquipment }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Pending Maintenance') }}</h6>
                            <h3 class="mb-0">{{ $pendingMaintenances }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Fuel Consumed') }}</h6>
                            <h3 class="mb-0">{{ $fuelConsumed }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Equipment') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.equipment-control.equipment.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Category') }}</label>
                            <input type="text" name="code" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Type') }}</label>
                            <input type="text" name="type" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="available">{{ __('Available') }}</option>
                                <option value="maintenance">{{ __('Maintenance') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Purchase Date') }}</label>
                            <input type="date" name="purchase_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Rental Rate') }}</label>
                            <input type="number" step="0.01" name="rental_rate" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Fuel Type') }}</label>
                            <input type="text" name="fuel_type" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Equipment') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Log Usage') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.equipment-control.usages.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Equipment') }}</label>
                            <select name="equipment_id" class="form-control" required>
                                @foreach ($equipments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Project') }}</label>
                            <select name="project_id" class="form-control" required>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Hours Used') }}</label>
                            <input type="number" step="0.1" name="hours_used" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Fuel Consumed') }}</label>
                            <input type="number" step="0.01" name="fuel_consumed" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Usage') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Schedule Maintenance') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.equipment-control.maintenances.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Equipment') }}</label>
                            <select name="equipment_id" class="form-control" required>
                                @foreach ($equipments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Scheduled Date') }}</label>
                            <input type="date" name="scheduled_at" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Type') }}</label>
                            <input type="text" name="maintenance_type" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Cost') }}</label>
                            <input type="number" step="0.01" name="cost" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Maintenance') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Equipment List') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipments as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ ucfirst($item->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No equipment found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Usage') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Equipment') }}</th>
                                <th>{{ __('Project') }}</th>
                                <th>{{ __('Start') }}</th>
                                <th>{{ __('End') }}</th>
                                <th>{{ __('Hours') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usages as $usage)
                                <tr>
                                    <td>{{ $equipments->firstWhere('id', $usage->equipment_id)?->name }}</td>
                                    <td>{{ $projects->firstWhere('id', $usage->project_id)?->project_name }}</td>
                                    <td>{{ \Auth::user()->dateFormat($usage->start_date) }}</td>
                                    <td>{{ \Auth::user()->dateFormat($usage->end_date) }}</td>
                                    <td>{{ $usage->hours_used }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No usage logs found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Upcoming Maintenance') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Equipment') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Cost') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($maintenances as $maintenance)
                                <tr>
                                    <td>{{ $equipments->firstWhere('id', $maintenance->equipment_id)?->name }}</td>
                                    <td>{{ \Auth::user()->dateFormat($maintenance->scheduled_at) }}</td>
                                    <td>{{ $maintenance->maintenance_type }}</td>
                                    <td>{{ \Auth::user()->priceFormat($maintenance->cost) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No maintenance scheduled.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
