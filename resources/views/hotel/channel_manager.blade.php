@extends('layouts.admin')

@section('page-title')
    {{ __('Channel Manager') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Channel Manager') }}</li>
@endsection

@section('action-btn')
    <form action="{{ route('hotel.channels.sync') }}" method="post">
        @csrf
        <button class="btn btn-sm btn-primary">{{ __('Sync All') }}</button>
    </form>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Add Channel') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.channels.store') }}" method="post">
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
                            <label class="form-label">{{ __('Active') }}</label>
                            <select name="is_active" class="form-control">
                                <option value="1">{{ __('Yes') }}</option>
                                <option value="0">{{ __('No') }}</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Sync Alerts') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($alerts as $alert)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $alert->name }}</span>
                            <span class="badge bg-danger">{{ $alert->sync_status }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No alerts.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Channel Performance') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Channel') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Reservations') }}</th>
                                <th>{{ __('Revenue') }}</th>
                                <th>{{ __('Last Sync') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($channels as $channel)
                                <tr>
                                    <td>{{ $channel->name }}</td>
                                    <td>{{ $channel->sync_status }}</td>
                                    <td>{{ optional($reservations->get($channel->id))->total ?? 0 }}</td>
                                    <td>{{ optional($reservations->get($channel->id))->revenue ?? 0 }}</td>
                                    <td>{{ $channel->last_synced_at ? $channel->last_synced_at->format('Y-m-d H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Sync Logs') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Channel') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Direction') }}</th>
                                <th>{{ __('Time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->channel?->name }}</td>
                                    <td>{{ $log->status }}</td>
                                    <td>{{ $log->direction }}</td>
                                    <td>{{ $log->synced_at ? $log->synced_at->format('Y-m-d H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
