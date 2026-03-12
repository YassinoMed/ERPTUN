@extends('layouts.admin')
@section('page-title', $apiClient->name)
@section('page-subtitle', __('Inspect API usage, error rates and credential governance for this integration client.'))
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="row mb-3">
            <div class="col-md-6"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Total calls') }}</span><h4 class="mb-0">{{ $stats['totalRequests'] }}</h4></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Last 24h') }}</span><h4 class="mb-0">{{ $stats['requests24h'] }}</h4></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Errors') }}</span><h4 class="mb-0">{{ $stats['errorRequests'] }}</h4></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-body"><span class="text-muted d-block small">{{ __('Routes') }}</span><h4 class="mb-0">{{ $stats['uniqueRoutes'] }}</h4></div></div></div>
        </div>
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Client Overview') }}</h5></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('Key') }}</dt>
                    <dd class="col-sm-8"><code>{{ $apiClient->client_key }}</code></dd>
                    <dt class="col-sm-4">{{ __('Status') }}</dt>
                    <dd class="col-sm-8"><span class="badge {{ $apiClient->is_active ? 'bg-success' : 'bg-danger' }}">{{ $apiClient->is_active ? __('Active') : __('Inactive') }}</span></dd>
                    <dt class="col-sm-4">{{ __('Abilities') }}</dt>
                    <dd class="col-sm-8">{{ implode(', ', $apiClient->abilities ?? []) ?: '-' }}</dd>
                    <dt class="col-sm-4">{{ __('Last Used') }}</dt>
                    <dd class="col-sm-8">{{ optional($apiClient->last_used_at)->diffForHumans() ?: '-' }}</dd>
                    <dt class="col-sm-4">{{ __('Expires') }}</dt>
                    <dd class="col-sm-8">{{ optional($apiClient->expires_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('api-clients.toggle-status', $apiClient) }}">@csrf <button class="btn btn-sm btn-secondary">{{ $apiClient->is_active ? __('Deactivate key') : __('Activate key') }}</button></form>
                <form method="POST" action="{{ route('api-clients.rotate-secret', $apiClient) }}">@csrf <button class="btn btn-sm btn-warning">{{ __('Rotate secret') }}</button></form>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('Authentication Headers') }}</h5></div>
            <div class="card-body"><pre class="small mb-0">X-Api-Client: {{ $apiClient->client_key }}
X-Api-Secret: ******** (shown only at creation)</pre></div>
        </div>
        <div class="card mt-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Ability Catalog') }}</h5></div>
            <div class="card-body small">
                @foreach($abilityCatalog as $ability => $description)
                    <div class="mb-2">
                        <code>{{ $ability }}</code>
                        <div class="text-muted">{{ $description }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('Recent API Logs') }}</h5></div>
            <div class="card-body table-border-style">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('When') }}</th>
                        <th>{{ __('Route') }}</th>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actor') }}</th>
                        <th>{{ __('IP') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ optional($log->requested_at)->format('Y-m-d H:i') }}</td>
                            <td><code>{{ $log->route }}</code></td>
                            <td>{{ $log->method }}</td>
                            <td>{{ $log->status_code ?: '-' }}</td>
                            <td>{{ optional($log->user)->name ?: '-' }}</td>
                            <td><code>{{ $log->ip_address ?: '-' }}</code></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">{{ __('No API traffic logged for this client yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
