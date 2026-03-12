@extends('layouts.admin')

@section('page-title')
    {{ __('Document Repository') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('document-repository.index') }}">{{ __('Document Repository') }}</a></li>
    <li class="breadcrumb-item">{{ $documentRepository->title }}</li>
@endsection

@section('content')
    @php
        $documentPath = \App\Models\Utility::get_file('uploads/document_repository');
        $documentUrl = $documentRepository->document ? $documentPath . '/' . $documentRepository->document : null;
        $extension = $documentRepository->document ? strtolower(pathinfo($documentRepository->document, PATHINFO_EXTENSION)) : null;
        $imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
    @endphp
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <strong>{{ __('Title') }}:</strong> {{ $documentRepository->title }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Category') }}:</strong> {{ optional($documentRepository->category)->name ?: '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Reference') }}:</strong> {{ $documentRepository->reference ?: '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Version') }}:</strong> {{ $documentRepository->version }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Status') }}:</strong> {{ __(ucfirst($documentRepository->status)) }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Effective Date') }}:</strong> {{ $documentRepository->effective_date ?: '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Expiry Date') }}:</strong> {{ $documentRepository->expires_at ?: '-' }}
                        </div>
                        <div class="col-md-12">
                            <strong>{{ __('Description') }}:</strong>
                            <p class="mb-0 mt-2">{{ $documentRepository->description ?: '-' }}</p>
                        </div>
                        <div class="col-md-12">
                            <strong>{{ __('Document') }}:</strong>
                            <div class="mt-2">
                                @if ($documentRepository->document)
                                    <a class="btn btn-sm btn-primary" href="{{ $documentUrl }}" download>
                                        <i class="ti ti-download"></i>
                                    </a>
                                    <a class="btn btn-sm btn-secondary" href="{{ $documentUrl }}" target="_blank">
                                        <i class="ti ti-crosshair"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        @if ($documentUrl)
                            <div class="col-md-12">
                                <strong>{{ __('Preview') }}:</strong>
                                <div class="mt-3 border rounded overflow-hidden bg-light">
                                    @if (in_array($extension, $imageExtensions, true))
                                        <img src="{{ $documentUrl }}" alt="{{ $documentRepository->title }}" class="img-fluid w-100">
                                    @elseif ($extension === 'pdf')
                                        <iframe src="{{ $documentUrl }}" title="{{ $documentRepository->title }}" style="width: 100%; min-height: 560px; border: 0;"></iframe>
                                    @else
                                        <div class="p-4 text-muted">
                                            {{ __('Preview is not available for this file type. Use open or download to inspect the file.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Document History') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($documentRepository->versions as $version)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-bold">{{ $version->version_label ?: __('Version') }}</div>
                                    <div class="text-muted small">{{ $version->file_name ?: '-' }}</div>
                                </div>
                                <span class="badge bg-light text-dark">{{ data_get($version->metadata, 'status', '-') }}</span>
                            </div>
                            <div class="small text-muted mt-2">
                                {{ optional($version->created_at)->format('Y-m-d H:i') ?: '-' }}
                            </div>
                            @if (data_get($version->metadata, 'source'))
                                <div class="small mt-1">{{ __('Source') }}: {{ data_get($version->metadata, 'source') }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No document history available yet.') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Linked Records') }}</h5>
                </div>
                <div class="card-body">
                    @forelse($documentRepository->links as $link)
                        <div class="border rounded p-3 mb-3">
                            <div class="fw-bold">{{ class_basename($link->linkable_type) }} #{{ $link->linkable_id }}</div>
                            <div class="small text-muted">{{ __('Relation') }}: {{ $link->relation_type ?: '-' }}</div>
                            <div class="small text-muted">{{ __('Linked by') }}: {{ $link->linked_by ?: '-' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('No cross-module links have been attached to this document.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
