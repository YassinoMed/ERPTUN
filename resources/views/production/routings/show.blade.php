@extends('layouts.admin')
@section('page-title')
    {{ $routing->name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('production.routings.index') }}">{{ __('Routings') }}</a></li>
    <li class="breadcrumb-item">{{ $routing->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ $routing->name }}</h5>
                    <p class="mb-1">{{ __('Code') }}: {{ $routing->code ?: '-' }}</p>
                    <p class="mb-1">{{ __('Product') }}: {{ $routing->product?->name ?: '-' }}</p>
                    <p class="mb-1">{{ __('Status') }}: {{ ucfirst($routing->status) }}</p>
                    <p class="mb-0">{{ __('Orders using this routing') }}: {{ $routing->orders->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Steps') }}</h5></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Work Center') }}</th>
                                    <th>{{ __('Resource') }}</th>
                                    <th>{{ __('Minutes') }}</th>
                                    <th>{{ __('Scrap %') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($routing->steps as $step)
                                    <tr>
                                        <td>{{ $step->sequence }}</td>
                                        <td>{{ $step->name }}</td>
                                        <td>{{ $step->workCenter?->name ?: '-' }}</td>
                                        <td>{{ $step->resource?->name ?: '-' }}</td>
                                        <td>{{ $step->planned_minutes }}</td>
                                        <td>{{ $step->scrap_percent }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
