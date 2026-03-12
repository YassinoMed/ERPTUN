@extends('layouts.admin')
@section('page-title')
    {{ __('Support Categories') }}
@endsection

@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Support Categories') }}</h5>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('support.index') }}">{{ __('Support') }}</a></li>
    <li class="breadcrumb-item">{{ __('Categories') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('support.index') }}" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip"
            title="{{ __('Back to Support') }}">
            <i class="ti ti-arrow-left"></i>
        </a>
        <a href="#" data-size="md" data-url="{{ route('support-categories.create') }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" title="{{ __('Create Category') }}" data-title="{{ __('Create Category') }}"
            class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Tickets') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="rounded-circle d-inline-block"
                                                    style="width:12px;height:12px;background-color: {{ $category->color }};"></span>
                                                <span>{{ $category->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $category->description ?: '-' }}</td>
                                        <td>
                                            @if ($category->is_active)
                                                <span class="badge bg-success p-2 px-3 rounded">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-secondary p-2 px-3 rounded">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $category->supports_count }}</td>
                                        <td class="Action">
                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                    data-url="{{ route('support-categories.edit', $category->id) }}"
                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                    title="{{ __('Edit') }}" data-title="{{ __('Edit Category') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['support-categories.destroy', $category->id]]) !!}
                                                <a href="#"
                                                    class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash text-white"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>
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
