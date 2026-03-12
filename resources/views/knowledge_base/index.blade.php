@extends('layouts.admin')

@section('page-title')
    {{ __('Knowledge Base') }}
@endsection
@section('page-subtitle')
    {{ __('Surface reusable answers, featured content and publication quality from one support knowledge hub.') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Knowledge Base') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex gap-2">
        @can('manage knowledge base category')
            <a href="{{ route('kb-categories.index') }}" class="btn btn-sm btn-primary-subtle">
                <i class="ti ti-category"></i>
            </a>
        @endcan
        @can('create knowledge base')
            <a href="#" data-url="{{ route('knowledge-base.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Article') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @php
        $publishedArticles = $articles->where('status', 'published')->count();
        $draftArticles = $articles->where('status', 'draft')->count();
        $featuredArticles = $articles->where('is_featured', 1)->count();
        $categoryCoverage = $articles->pluck('knowledge_base_category_id')->filter()->unique()->count();
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="ux-kpi-grid mb-4">
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Published') }}</span>
                    <strong class="ux-kpi-value">{{ $publishedArticles }}</strong>
                    <span class="ux-kpi-meta">{{ __('live knowledge articles') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Drafts') }}</span>
                    <strong class="ux-kpi-value">{{ $draftArticles }}</strong>
                    <span class="ux-kpi-meta">{{ __('still being prepared') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Featured') }}</span>
                    <strong class="ux-kpi-value">{{ $featuredArticles }}</strong>
                    <span class="ux-kpi-meta">{{ __('priority articles') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Active categories') }}</span>
                    <strong class="ux-kpi-value">{{ $categoryCoverage }}</strong>
                    <span class="ux-kpi-meta">{{ __('taxonomy coverage') }}</span>
                </div>
            </div>
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Featured') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articles as $article)
                                    <tr data-bulk-id="{{ $article->id }}">
                                        <td>{{ $article->title }}</td>
                                        <td>{{ optional($article->category)->name ?: '-' }}</td>
                                        <td>{{ __(ucfirst($article->status)) }}</td>
                                        <td>{{ $article->is_featured ? __('Yes') : __('No') }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('knowledge-base.show', $article->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit knowledge base')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('knowledge-base/' . $article->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Article') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete knowledge base')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['knowledge-base.destroy', $article->id], 'id' => 'delete-form-' . $article->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $article->id }}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
