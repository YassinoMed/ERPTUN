@extends('layouts.admin')
@section('page-title', __('Help Center'))
@section('page-subtitle', __('Search published knowledge base content without leaving the workspace.'))
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><h5>{{ __('Guided Topics') }}</h5></div>
            <div class="card-body">
                <p class="text-muted">{{ __('Shortcuts to the most important tenant onboarding, API and security help content.') }}</p>
                <div class="list-group">
                    @forelse($guidedArticles as $article)
                        <a href="{{ route('knowledge-base.show', $article) }}" class="list-group-item list-group-item-action">
                            <strong>{{ $article->title }}</strong>
                            <div class="text-muted small">{{ $article->summary }}</div>
                        </a>
                    @empty
                        <div class="text-muted">{{ __('No guided articles available yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>{{ __('Integrated Help') }}</h5></div>
            <div class="card-body">
                <p>{{ __('This center aggregates published knowledge-base content for tenant users.') }}</p>
                @can('manage security center')
                    <div class="alert alert-info">
                        <a href="{{ route('core.consolidation') }}" class="alert-link">{{ __('Open the consolidation cockpit') }}</a>
                        {{ __('to review production readiness, API governance and cross-module health.') }}
                    </div>
                @endcan
                <form method="GET" action="{{ route('core.help-center') }}" class="row g-2 mb-3">
                    <div class="col-md-10"><input type="text" name="q" class="form-control" value="{{ $search }}" placeholder="{{ __('Search help articles, onboarding, modules...') }}"></div>
                    <div class="col-md-2 d-grid"><button class="btn btn-light">{{ __('Search') }}</button></div>
                </form>
                <div class="list-group">
                    @forelse($articles as $article)
                        <a href="{{ route('knowledge-base.show', $article) }}" class="list-group-item list-group-item-action">
                            <strong>{{ $article->title }}</strong>
                            <div class="text-muted small">{{ $article->summary }}</div>
                        </a>
                    @empty
                        <div class="text-muted">{{ __('No published help articles available.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
