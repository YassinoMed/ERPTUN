@extends('layouts.admin')

@section('page-title', __('Core Consolidation'))
@section('page-subtitle', __('Review cross-module readiness, security posture, API operations and production checklist from a single control point.'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Core Consolidation') }}</li>
@endsection

@section('action-button')
    <div class="d-flex gap-2">
        <a href="{{ route('core.security.index') }}" class="btn btn-sm btn-outline-primary">{{ __('Security Center') }}</a>
        <a href="{{ route('api-clients.index') }}" class="btn btn-sm btn-outline-primary">{{ __('API Clients') }}</a>
        <a href="{{ route('approval-requests.index') }}" class="btn btn-sm btn-outline-primary">{{ __('Approvals') }}</a>
    </div>
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Pending approvals') }}</span><strong class="ux-kpi-value">{{ $metrics['pending_approvals'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Automation failures') }}</span><strong class="ux-kpi-value">{{ $metrics['automation_failures'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Active API clients') }}</span><strong class="ux-kpi-value">{{ $metrics['api_clients_active'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('API logs today') }}</span><strong class="ux-kpi-value">{{ $metrics['api_logs_today'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Sensitive access today') }}</span><strong class="ux-kpi-value">{{ $metrics['sensitive_access_today'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Notification backlog') }}</span><strong class="ux-kpi-value">{{ $metrics['notification_backlog'] }}</strong></div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header"><h5>{{ __('Cross-Module Health') }}</h5></div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Domain') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Volume') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleHealth as $domain => $item)
                                <tr>
                                    <td>{{ ucfirst($domain) }}</td>
                                    <td>
                                        <span class="badge {{ $item['status'] === 'ready' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($item['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $item['volume'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Production Readiness Checklist') }}</h5></div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($checklist as $item)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ __($item['name']) }}</strong>
                                    <span class="badge {{ $item['status'] === 'ready' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($item['status']) }}
                                    </span>
                                </div>
                                <div class="text-muted small mt-1">{{ __($item['detail']) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header"><h5>{{ __('Operational Guardrails') }}</h5></div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>{{ __('Warm caches after deployments that affect menus, permissions or settings.') }}</li>
                        <li>{{ __('Review API logs and failed automations every day before tenant traffic peaks.') }}</li>
                        <li>{{ __('Keep security scopes aligned with branch, warehouse and department changes.') }}</li>
                        <li>{{ __('Review saved reports and scheduled exports after any schema update.') }}</li>
                        <li>{{ __('Validate sensitive access logs on medical, document and finance screens.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>{{ __('Execution Links') }}</h5></div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('core.onboarding') }}" class="btn btn-light">{{ __('Tenant Onboarding Cockpit') }}</a>
                    <a href="{{ route('core.addons.index') }}" class="btn btn-light">{{ __('Plan Addons') }}</a>
                    <a href="{{ route('core.saved-views.index') }}" class="btn btn-light">{{ __('Saved Views') }}</a>
                    <a href="{{ route('core.preferences') }}" class="btn btn-light">{{ __('User Preferences') }}</a>
                    <a href="{{ route('core.help-center') }}" class="btn btn-light">{{ __('Help Center') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
