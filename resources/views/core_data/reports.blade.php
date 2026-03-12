@extends('layouts.admin')
@section('page-title', __('Saved Reports'))
@section('page-subtitle', __('Share operational reports, track usage and schedule recurring deliveries.'))
@section('action-btn')<a href="{{ route('core.reports.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endsection
@section('content')
<div class="row mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Reports') }}</span><h3 class="mb-0">{{ $reportStats['total'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Shared') }}</span><h3 class="mb-0">{{ $reportStats['shared'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Schedules') }}</span><h3 class="mb-0">{{ $reportStats['scheduled'] }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Active schedules') }}</span><h3 class="mb-0">{{ $reportStats['activeSchedules'] }}</h3></div></div></div>
</div>
<div class="d-flex justify-content-end mb-3">
    <form method="POST" action="{{ route('core.reports.schedule.dispatch-due') }}">
        @csrf
        <button class="btn btn-sm btn-primary">{{ __('Queue Due Report Deliveries') }}</button>
    </form>
</div>
<div class="card"><div class="card-body table-border-style"><table class="table"><thead><tr><th>{{ __('Name') }}</th><th>{{ __('Type') }}</th><th>{{ __('Shared') }}</th><th>{{ __('Last Run') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@foreach($reports as $report)<tr><td>{{ $report->name }}</td><td>{{ $report->report_type }}</td><td>{{ $report->is_shared ? __('Yes') : __('No') }}</td><td>{{ optional($report->last_run_at)->diffForHumans() ?: '-' }}</td><td><a class="btn btn-sm btn-warning" href="{{ route('core.reports.show', $report) }}">{{ __('Open') }}</a></td></tr>@endforeach</tbody></table></div></div>
@endsection
