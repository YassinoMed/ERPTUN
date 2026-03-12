@extends('layouts.admin')
@section('page-title', __('NPS Campaigns'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('NPS Campaigns') }}</li>
@endsection
@section('page-subtitle')
    {{ __('Monitor customer sentiment and feedback loops from a dedicated commercial feedback workspace.') }}
@endsection
@section('action-btn')
    <div class="float-end">@can('create nps campaign')<a href="{{ route('nps-campaigns.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>@endcan</div>
@endsection
@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Campaigns') }}</span><strong class="ux-kpi-value">{{ $campaigns->count() }}</strong><span class="ux-kpi-meta">{{ __('feedback programs') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Responses') }}</span><strong class="ux-kpi-value">{{ $campaigns->sum('responses_count') }}</strong><span class="ux-kpi-meta">{{ __('voice of customer inputs') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Active') }}</span><strong class="ux-kpi-value">{{ $campaigns->where('status', 'active')->count() }}</strong><span class="ux-kpi-meta">{{ __('currently collecting answers') }}</span></div>
    </div>
    <div class="card ux-list-card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table datatable"><thead><tr><th>{{ __('Campaign') }}</th><th>{{ __('Channel') }}</th><th>{{ __('Status') }}</th><th>{{ __('Responses') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@foreach($campaigns as $campaign)<tr><td><div>{{ $campaign->name }}</div><small class="text-muted">{{ $campaign->audience_type }}</small></td><td>{{ strtoupper($campaign->channel) }}</td><td><span class="badge bg-info">{{ __(ucfirst($campaign->status)) }}</span></td><td>{{ $campaign->responses_count }}</td><td class="Action"><div class="action-btn me-2"><a href="{{ route('nps-campaigns.show', $campaign) }}" class="mx-3 btn btn-sm bg-warning"><i class="ti ti-eye text-white"></i></a></div>@can('edit nps campaign')<div class="action-btn me-2"><a href="{{ route('nps-campaigns.edit', $campaign) }}" class="mx-3 btn btn-sm bg-info"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete nps campaign')<div class="action-btn"><form method="POST" action="{{ route('nps-campaigns.destroy', $campaign) }}" id="delete-nps-campaign-{{ $campaign->id }}">@csrf @method('DELETE')<a href="#" class="mx-3 btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-nps-campaign-{{ $campaign->id }}').submit();"><i class="ti ti-trash text-white"></i></a></form></div>@endcan</td></tr>@endforeach</tbody></table></div></div></div>
@endsection
