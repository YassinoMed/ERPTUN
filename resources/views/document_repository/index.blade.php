@extends('layouts.admin')

@section('page-title')
    {{ __('Document Repository') }}
@endsection
@section('page-subtitle')
    {{ __('Manage controlled documents, versioning and access to business-critical files from one repository.') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Document Repository') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex gap-2">
        @can('manage document repository category')
            <a href="{{ route('document-repository-categories.index') }}" class="btn btn-sm btn-primary-subtle">
                <i class="ti ti-category"></i>
            </a>
        @endcan
        @can('create document repository')
            <a href="#" data-url="{{ route('document-repository.create') }}" data-size="lg" data-ajax-popup="true"
                data-title="{{ __('Create Document') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    @php
        $approvedDocuments = $documents->where('status', 'approved')->count();
        $draftDocuments = $documents->where('status', 'draft')->count();
        $archivedDocuments = $documents->where('status', 'archived')->count();
        $versionedDocuments = $documents->filter(function ($document) {
            return (float) ($document->version ?? 1) > 1;
        })->count();
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="ux-kpi-grid mb-4">
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Approved') }}</span>
                    <strong class="ux-kpi-value">{{ $approvedDocuments }}</strong>
                    <span class="ux-kpi-meta">{{ __('ready for operational use') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Drafts') }}</span>
                    <strong class="ux-kpi-value">{{ $draftDocuments }}</strong>
                    <span class="ux-kpi-meta">{{ __('pending review') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Archived') }}</span>
                    <strong class="ux-kpi-value">{{ $archivedDocuments }}</strong>
                    <span class="ux-kpi-meta">{{ __('retained history') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Versioned files') }}</span>
                    <strong class="ux-kpi-value">{{ $versionedDocuments }}</strong>
                    <span class="ux-kpi-meta">{{ __('documents above v1') }}</span>
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
                                    <th>{{ __('Reference') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Document') }}</th>
                                    <th width="220px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    @php
                                        $documentPath = \App\Models\Utility::get_file('uploads/document_repository');
                                    @endphp
                                    <tr data-bulk-id="{{ $document->id }}">
                                        <td>{{ $document->title }}</td>
                                        <td>{{ optional($document->category)->name ?: '-' }}</td>
                                        <td>{{ $document->reference ?: '-' }}</td>
                                        <td>{{ $document->version }}</td>
                                        <td>{{ __(ucfirst($document->status)) }}</td>
                                        <td>
                                            @if ($document->document)
                                                <div class="d-flex gap-2">
                                                    <a class="btn btn-sm align-items-center bg-primary"
                                                        href="{{ $documentPath . '/' . $document->document }}" download>
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                    <a class="btn btn-sm align-items-center bg-secondary"
                                                        href="{{ $documentPath . '/' . $document->document }}" target="_blank">
                                                        <i class="ti ti-crosshair text-white"></i>
                                                    </a>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="{{ route('document-repository.show', $document->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center bg-warning">
                                                    <i class="ti ti-eye text-white"></i>
                                                </a>
                                            </div>
                                            @can('edit document repository')
                                                <div class="action-btn me-2">
                                                    <a href="#"
                                                        data-url="{{ URL::to('document-repository/' . $document->id . '/edit') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Document') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete document repository')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['document-repository.destroy', $document->id], 'id' => 'delete-form-' . $document->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $document->id }}').submit();">
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
