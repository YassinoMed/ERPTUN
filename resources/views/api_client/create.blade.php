@extends('layouts.admin')
@section('page-title', __('Create API Client'))
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('api-clients.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Abilities') }}</label>
                    <input type="text" name="abilities" class="form-control" placeholder="customers:read,products:read,invoices:read">
                    <div class="form-text">{{ __('Use comma-separated abilities from the catalog below.') }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Expires At') }}</label>
                    <input type="date" name="expires_at" class="form-control">
                </div>
            </div>
            <div class="border rounded p-3 mb-3 bg-light">
                <div class="fw-semibold mb-2">{{ __('Supported abilities') }}</div>
                <div class="row">
                    @foreach($abilityCatalog as $ability => $description)
                        <div class="col-md-6 small mb-2">
                            <code>{{ $ability }}</code>
                            <div class="text-muted">{{ $description }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <button class="btn btn-primary">{{ __('Create Client') }}</button>
        </form>
    </div>
</div>
@endsection
