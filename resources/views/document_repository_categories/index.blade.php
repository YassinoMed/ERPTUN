@extends('layouts.admin')

@section('page-title')
    {{ __('Document Repository Categories') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('document-repository.index') }}">{{ __('Document Repository') }}</a></li>
    <li class="breadcrumb-item">{{ __('Categories') }}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create document repository category')
            <a href="#" data-url="{{ route('document-repository-categories.create') }}" data-size="md"
                data-ajax-popup="true" data-title="{{ __('Create Category') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th>{{ __('Documents') }}</th>
                                    <th width="160px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->is_active ? __('Yes') : __('No') }}</td>
                                        <td>{{ $category->documents_count }}</td>
                                        <td class="Action">
                                            @can('edit document repository category')
                                                <div class="action-btn me-2">
                                                    <a href="#"
                                                        data-url="{{ URL::to('document-repository-categories/' . $category->id . '/edit') }}"
                                                        data-size="md" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Category') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete document repository category')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['document-repository-categories.destroy', $category->id], 'id' => 'delete-form-' . $category->id]) !!}
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="document.getElementById('delete-form-{{ $category->id }}').submit();">
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
