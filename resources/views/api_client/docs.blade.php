@extends('layouts.admin')
@section('page-title', __('API Client Documentation'))
@section('page-subtitle', __('Dedicated client endpoints use long-lived integration credentials, request logging and scoped abilities.'))
@section('content')
<div class="card"><div class="card-body">
    <p>{{ __('Use X-Api-Client and X-Api-Secret headers for dedicated client endpoints.') }}</p>
    <div class="mb-3">
        <div class="fw-semibold">{{ __('Authentication Headers') }}</div>
        <pre class="small mb-0">X-Api-Client: erp_xxxxxxxxxxxxxxxxxxxxxxxx
X-Api-Secret: your-issued-plain-secret</pre>
    </div>
    <div class="mb-3">
        <div class="fw-semibold">{{ __('Dedicated client endpoints') }}</div>
        <ul class="mb-0">
            <li><code>GET /api/client/v1/customers</code> <span class="text-muted">customers:read</span></li>
            <li><code>GET /api/client/v1/products</code> <span class="text-muted">products:read</span></li>
            <li><code>GET /api/client/v1/invoices</code> <span class="text-muted">invoices:read</span></li>
            <li><code>GET /api/client/v1/purchases</code> <span class="text-muted">purchases:read</span></li>
            <li><code>GET /api/client/v1/patients</code> <span class="text-muted">patients:read</span></li>
            <li><code>GET /api/client/v1/delivery-notes</code> <span class="text-muted">delivery-notes:read</span></li>
        </ul>
    </div>
    <div class="mb-3">
        <div class="fw-semibold">{{ __('Conventions') }}</div>
        <ul class="mb-0">
            <li>{{ __('Dedicated integration endpoints use the /api/client/v1 prefix and always return the same response envelope.') }}</li>
            <li>{{ __('User-scoped Sanctum endpoints remain available for interactive clients, but should progressively follow the same resource naming and pagination semantics.') }}</li>
            <li>{{ __('Rotate secrets from the API Clients screen whenever a partner or connector is rotated.') }}</li>
        </ul>
    </div>
    <div class="mb-3">
        <div class="fw-semibold">{{ __('Response envelope') }}</div>
        <pre class="small mb-0">{
  "success": true,
  "request_id": "uuid",
  "resource": "customers",
  "client": "erp_xxxxxxxxxxxxxxxxxxxxxxxx",
  "data": [ ... items ... ],
  "meta": {
    "filters": { "q": null, "per_page": 20 },
    "pagination": { "current_page": 1, "per_page": 20, "total": 42, "last_page": 3 }
  }
}</pre>
    </div>
    <div class="mb-3">
        <div class="fw-semibold">{{ __('Supported abilities') }}</div>
        <ul class="mb-0">
            @foreach($abilityCatalog as $ability => $description)
                <li><code>{{ $ability }}</code> <span class="text-muted">{{ $description }}</span></li>
            @endforeach
        </ul>
    </div>
    <div class="mb-3">
        <div class="fw-semibold">{{ __('Standard user-scoped API') }}</div>
        <p class="mb-0">{{ __('Sanctum endpoints remain available in parallel for authenticated user tokens, including tenant-scoped finance routes and module APIs.') }}</p>
    </div>
    <div>
        <div class="fw-semibold">{{ __('Logging & governance') }}</div>
        <p class="mb-0">{{ __('Every API request is logged with route, method, status code, IP and payload preview. Use the API Clients screen to inspect recent traffic.') }}</p>
    </div>
</div></div>
@endsection
