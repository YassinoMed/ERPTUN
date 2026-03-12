@extends('layouts.admin')
@section('page-title', __('Export Job').' #'.$exportJob->id)
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Execution Summary') }}</h5></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">{{ __('Module') }}</dt>
                    <dd class="col-sm-7">{{ $exportJob->module }}</dd>
                    <dt class="col-sm-5">{{ __('Format') }}</dt>
                    <dd class="col-sm-7">{{ strtoupper($exportJob->format) }}</dd>
                    <dt class="col-sm-5">{{ __('Status') }}</dt>
                    <dd class="col-sm-7"><span class="badge bg-light text-dark">{{ ucfirst($exportJob->status) }}</span></dd>
                    <dt class="col-sm-5">{{ __('Scheduled') }}</dt>
                    <dd class="col-sm-7">{{ optional($exportJob->scheduled_for)->format('Y-m-d H:i') ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Started') }}</dt>
                    <dd class="col-sm-7">{{ optional($exportJob->started_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Completed') }}</dt>
                    <dd class="col-sm-7">{{ optional($exportJob->completed_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                    <dt class="col-sm-5">{{ __('Attempts') }}</dt>
                    <dd class="col-sm-7">{{ $exportJob->attempts }}</dd>
                    <dt class="col-sm-5">{{ __('File') }}</dt>
                    <dd class="col-sm-7 text-break">{{ $exportJob->file_path ?: '-' }}</dd>
                </dl>
                <div class="d-flex gap-2 flex-wrap mt-3">
                    <form method="POST" action="{{ route('core.exports.run', $exportJob) }}">@csrf <button class="btn btn-primary">{{ __('Run Now') }}</button></form>
                    @if($exportJob->file_path)
                        <a href="{{ route('core.exports.download', $exportJob) }}" class="btn btn-light">{{ __('Download') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Filters') }}</h5></div>
            <div class="card-body"><pre class="small mb-0">{{ json_encode($exportJob->filters, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('Error / Diagnostics') }}</h5></div>
            <div class="card-body">
                @if($exportJob->error_message)
                    <div class="alert alert-danger mb-0">{{ $exportJob->error_message }}</div>
                @else
                    <div class="text-muted">{{ __('No execution errors recorded for this export job.') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
