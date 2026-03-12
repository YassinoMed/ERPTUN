@extends('layouts.admin')
@section('page-title', __('Export Jobs'))
@section('page-subtitle', __('Schedule exports, rerun failed jobs and download generated files from one queue.'))
@section('action-btn')<a href="{{ route('core.exports.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endsection
@section('content')
<div class="row mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Queued') }}</span><h3 class="mb-0">{{ $exportStats['queued'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Processing') }}</span><h3 class="mb-0">{{ $exportStats['processing'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Completed') }}</span><h3 class="mb-0">{{ $exportStats['completed'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Failed') }}</span><h3 class="mb-0">{{ $exportStats['failed'] }}</h3></div></div></div>
</div>
<div class="d-flex justify-content-end mb-3">
    <form method="POST" action="{{ route('core.exports.dispatch-due') }}">
        @csrf
        <button class="btn btn-sm btn-primary">{{ __('Queue Due Exports') }}</button>
    </form>
</div>
<div class="card">
    <div class="card-body table-border-style">
        <table class="table">
            <thead><tr><th>ID</th><th>{{ __('Module') }}</th><th>{{ __('Format') }}</th><th>{{ __('Status') }}</th><th>{{ __('Scheduled') }}</th><th>{{ __('Attempts') }}</th><th>{{ __('Action') }}</th></tr></thead>
            <tbody>
            @foreach($exports as $job)
                <tr>
                    <td>#{{ $job->id }}</td>
                    <td>{{ $job->module }}</td>
                    <td>{{ strtoupper($job->format) }}</td>
                    <td>
                        <span class="badge {{ $job->status === 'completed' ? 'bg-success' : ($job->status === 'failed' ? 'bg-danger' : ($job->status === 'processing' ? 'bg-info text-dark' : 'bg-warning text-dark')) }}">
                            {{ ucfirst($job->status) }}
                        </span>
                    </td>
                    <td>{{ optional($job->scheduled_for)->format('Y-m-d H:i') ?: '-' }}</td>
                    <td>{{ $job->attempts }}</td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('core.exports.show', $job) }}" class="btn btn-sm btn-light">{{ __('Open') }}</a>
                            <form method="POST" action="{{ route('core.exports.run', $job) }}">@csrf <button class="btn btn-sm btn-primary">{{ __('Run') }}</button></form>
                            @if($job->file_path)
                                <a href="{{ route('core.exports.download', $job) }}" class="btn btn-sm btn-success">{{ __('Download') }}</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $exports->links() }}
    </div>
</div>
@endsection
