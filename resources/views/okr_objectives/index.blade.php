@extends('layouts.admin')

@section('page-title', __('OKR Workspace'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('OKR Workspace') }}</li>
@endsection
@section('page-subtitle')
    {{ __('Track strategic objectives, progress and key results in one review-ready workspace.') }}
@endsection
@section('action-btn')
    <div class="float-end">@can('create okr objective')<a href="{{ route('okr-objectives.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endcan</div>
@endsection
@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Objectives') }}</span><strong class="ux-kpi-value">{{ $objectives->count() }}</strong><span class="ux-kpi-meta">{{ __('active review scope') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Key Results') }}</span><strong class="ux-kpi-value">{{ $objectives->sum('key_results_count') }}</strong><span class="ux-kpi-meta">{{ __('tracked outcomes') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('At risk') }}</span><strong class="ux-kpi-value">{{ $objectives->where('status', 'at_risk')->count() }}</strong><span class="ux-kpi-meta">{{ __('requires leadership attention') }}</span></div>
    </div>
    <div class="card ux-list-card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table datatable"><thead><tr><th>{{ __('Objective') }}</th><th>{{ __('Owner') }}</th><th>{{ __('Cycle') }}</th><th>{{ __('Progress') }}</th><th>{{ __('Key Results') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@foreach($objectives as $objective)<tr><td><div>{{ $objective->title }}</div><small class="text-muted">{{ optional($objective->project)->project_name ?: __('No linked project') }}</small></td><td>{{ optional($objective->owner)->name ?: '-' }}</td><td>{{ $objective->cycle ?: '-' }}</td><td>{{ number_format((float) $objective->progress, 0) }}%</td><td>{{ $objective->key_results_count }}</td><td class="Action"><div class="action-btn me-2"><a href="{{ route('okr-objectives.show', $objective) }}" class="mx-3 btn btn-sm bg-warning"><i class="ti ti-eye text-white"></i></a></div>@can('edit okr objective')<div class="action-btn me-2"><a href="{{ route('okr-objectives.edit', $objective) }}" class="mx-3 btn btn-sm bg-info"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete okr objective')<div class="action-btn"><form method="POST" action="{{ route('okr-objectives.destroy', $objective) }}" id="delete-okr-objective-{{ $objective->id }}">@csrf @method('DELETE')<a href="#" class="mx-3 btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-okr-objective-{{ $objective->id }}').submit();"><i class="ti ti-trash text-white"></i></a></form></div>@endcan</td></tr>@endforeach</tbody></table></div></div></div>
@endsection
