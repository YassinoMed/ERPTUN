@extends('layouts.admin')

@section('page-title')
    {{ __('BTP Site Tracking') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('BTP Site Tracking') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @if ($ganttUrl)
            <a href="{{ $ganttUrl }}" class="btn btn-sm btn-primary">
                <span class="btn-inner--icon"><i class="ti ti-chart-bar"></i></span>
                <span class="btn-inner--text">{{ __('Gantt Chart') }}</span>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Photos') }}</h6>
                            <h3 class="mb-0">{{ $totalPhotos }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Tasks') }}</h6>
                            <h3 class="mb-0">{{ $taskStats['total'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Completed') }}</h6>
                            <h3 class="mb-0">{{ $taskStats['completed'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Delayed') }}</h6>
                            <h3 class="mb-0">{{ $taskStats['delayed'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Project Filter') }}</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('btp.site-tracking.index') }}">
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Project') }}</label>
                            <select name="project_id" class="form-control">
                                <option value="">{{ __('All Projects') }}</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @if ($selectedProject && $selectedProject->id === $project->id) selected @endif>
                                        {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Apply') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Upload Site Photo') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.site-tracking.photos.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Project') }}</label>
                            <select name="project_id" class="form-control" required>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Photo') }}</label>
                            <input type="file" name="photo" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Taken At') }}</label>
                            <input type="datetime-local" name="taken_at" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Latitude') }}</label>
                            <input type="number" step="0.0000001" name="latitude" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Longitude') }}</label>
                            <input type="number" step="0.0000001" name="longitude" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Photo') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Photos') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Photo') }}</th>
                                <th>{{ __('Project') }}</th>
                                <th>{{ __('Taken At') }}</th>
                                <th>{{ __('Location') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($photos as $photo)
                                <tr>
                                    <td>
                                        <img src="{{ \App\Models\Utility::get_file($photo->file) }}" alt="photo" class="rounded" width="60" height="60">
                                    </td>
                                    <td>{{ $projects->firstWhere('id', $photo->project_id)?->project_name }}</td>
                                    <td>{{ $photo->taken_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $photo->latitude }} {{ $photo->longitude }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No photos found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Delayed Tasks') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Task') }}</th>
                                <th>{{ __('Project') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Priority') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($delayedTasks as $task)
                                <tr>
                                    <td>{{ $task->name }}</td>
                                    <td>{{ $selectedProject?->project_name }}</td>
                                    <td>{{ \App\Models\Utility::getDateFormated($task->end_date) }}</td>
                                    <td>{{ ucfirst($task->priority) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No delayed tasks.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
