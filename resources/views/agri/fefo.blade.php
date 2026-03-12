@extends('layouts.admin')

@section('page-title')
    {{ __('FEFO Board') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('FEFO Board') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Prioritize stored lots by expiry date to support FEFO picking, cold-chain discipline and spoilage reduction.') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('Expiry Priority Queue') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Lot') }}</th>
                        <th>{{ __('Facility') }}</th>
                        <th>{{ __('Expiry Date') }}</th>
                        <th>{{ __('Days to Expiry') }}</th>
                        <th>{{ __('Risk') }}</th>
                        <th>{{ __('Qty') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $row)
                        <tr>
                            <td>{{ optional($row['record']->lot)->code ?: '-' }}</td>
                            <td>{{ $row['record']->facility_name }}</td>
                            <td>{{ optional($row['record']->expiry_date)->format('Y-m-d') ?: '-' }}</td>
                            <td>{{ $row['days_to_expiry'] }}</td>
                            <td><span class="badge bg-{{ $row['risk'] === 'critical' ? 'danger' : ($row['risk'] === 'warning' ? 'warning' : 'success') }}">{{ ucfirst($row['risk']) }}</span></td>
                            <td>{{ $row['record']->quantity }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">{{ __('No FEFO records available.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
