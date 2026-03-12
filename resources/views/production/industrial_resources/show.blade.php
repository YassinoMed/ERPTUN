@extends('layouts.admin')
@section('page-title')
    {{ $resource->name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Production') }}</li>
    <li class="breadcrumb-item"><a href="{{ route('production.resources.index') }}">{{ __('Industrial Resources') }}</a></li>
    <li class="breadcrumb-item">{{ $resource->name }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ $resource->name }}</h5>
                    <p class="mb-1">{{ __('Type') }}: {{ ucfirst($resource->type) }}</p>
                    <p class="mb-1">{{ __('Code') }}: {{ $resource->code ?: '-' }}</p>
                    <p class="mb-1">{{ __('Status') }}: {{ ucfirst($resource->status) }}</p>
                    <p class="mb-1">{{ __('Parent') }}: {{ $resource->parent?->name ?: '-' }}</p>
                    <p class="mb-1">{{ __('Branch') }}: {{ $resource->branch?->name ?: '-' }}</p>
                    <p class="mb-1">{{ __('Manager') }}: {{ $resource->manager?->name ?: '-' }}</p>
                    <p class="mb-0">{{ __('Capacity') }}: {{ $resource->capacity_hours_per_day }}h / {{ $resource->capacity_workers }} {{ __('workers') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Work Centers') }}</h5></div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Machine Code') }}</th>
                                    <th>{{ __('Cost / Hour') }}</th>
                                    <th>{{ __('Bottleneck') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($resource->workCenters as $workCenter)
                                    <tr>
                                        <td>{{ $workCenter->name }}</td>
                                        <td>{{ $workCenter->machine_code ?: '-' }}</td>
                                        <td>{{ \Auth::user()->priceFormat($workCenter->cost_per_hour) }}</td>
                                        <td>{{ $workCenter->is_bottleneck ? __('Yes') : __('No') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">{{ __('No work centers linked.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
