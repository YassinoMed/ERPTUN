@extends('layouts.admin')

@section('page-title')
    {{ __('Knowledge Base Article') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('knowledge-base.index') }}">{{ __('Knowledge Base') }}</a></li>
    <li class="breadcrumb-item">{{ __('Article') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header"><h5>{{ $knowledgeBase->title }}</h5></div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6"><strong>{{ __('Category') }}:</strong> {{ optional($knowledgeBase->category)->name ?: '-' }}</div>
                        <div class="col-md-3"><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($knowledgeBase->status)) }}</div>
                        <div class="col-md-3"><strong>{{ __('Featured') }}:</strong> {{ $knowledgeBase->is_featured ? __('Yes') : __('No') }}</div>
                        <div class="col-12"><strong>{{ __('Summary') }}:</strong><p class="text-muted mb-0">{{ $knowledgeBase->summary ?: '-' }}</p></div>
                        <div class="col-12"><strong>{{ __('Content') }}:</strong><div class="text-muted">{!! nl2br(e($knowledgeBase->content ?: '-')) !!}</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
