@extends('layouts.admin')
@section('page-title', __('Import Job').' #'.$importJob->id)
@section('page-subtitle', __('Validate mappings, inspect preview rows and roll back imports when needed.'))
@section('content')
<div class="row">
<div class="col-md-7">
    <div class="card mb-3">
        <div class="card-header"><h5>{{ __('Import Overview') }}</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><span class="text-muted d-block small">{{ __('Module') }}</span><strong>{{ $importJob->module }}</strong></div>
                <div class="col-md-4"><span class="text-muted d-block small">{{ __('Status') }}</span><strong>{{ ucfirst($importJob->status) }}</strong></div>
                <div class="col-md-4"><span class="text-muted d-block small">{{ __('Processed') }}</span><strong>{{ optional($importJob->processed_at)->format('Y-m-d H:i') ?: '-' }}</strong></div>
            </div>
        </div>
    </div>
    <div class="card"><div class="card-header"><h5>{{ __('Preview') }}</h5></div><div class="card-body"><pre class="small">{{ json_encode($importJob->preview_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></div></div>
</div>
<div class="col-md-5">
    <div class="card"><div class="card-header"><h5>{{ __('Column Mapping') }}</h5></div><div class="card-body">
        <form method="POST" action="{{ route('core.imports.mapping', $importJob) }}">@csrf
            @foreach((data_get($importJob->preview_data, 'headers', [])) as $header)
                <div class="mb-2"><label class="form-label">{{ $header }}</label><input type="text" name="mapping[{{ $header }}]" class="form-control" value="{{ data_get($importJob->mapping, $header) }}"></div>
            @endforeach
            <button class="btn btn-primary">{{ __('Save Mapping') }}</button>
        </form>
        <div class="alert alert-light mt-3">
            <div class="fw-semibold mb-1">{{ __('Rollback payload') }}</div>
            <div class="small text-muted">{{ __('Created record IDs are stored to support module-aware rollback when available.') }}</div>
        </div>
        <form method="POST" action="{{ route('core.imports.rollback', $importJob) }}" class="mt-3">@csrf <button class="btn btn-danger">{{ __('Rollback Import') }}</button></form>
    </div></div>
</div>
</div>
@endsection
