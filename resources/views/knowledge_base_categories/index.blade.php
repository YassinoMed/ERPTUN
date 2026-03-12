@extends('layouts.admin')

@section('page-title')
    {{ __('Knowledge Base Categories') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('knowledge-base.index') }}">{{ __('Knowledge Base') }}</a></li>
    <li class="breadcrumb-item">{{ __('Categories') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create knowledge base category')
            <a href="#" data-url="{{ route('kb-categories.create') }}" data-size="md" data-ajax-popup="true"
                data-title="{{ __('Create Category') }}" class="btn btn-sm btn-primary">
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
                                    <th>{{ __('Articles') }}</th>
                                    <th width="180px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->is_active ? __('Yes') : __('No') }}</td>
                                        <td>{{ $category->articles_count }}</td>
                                        <td class="Action">
                                            @can('edit knowledge base category')
                                                <div class="action-btn me-2">
                                                    <a href="#" data-url="{{ URL::to('kb-categories/' . $category->id . '/edit') }}"
                                                        data-size="md" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Category') }}"
                                                        class="mx-3 btn btn-sm align-items-center bg-info">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete knowledge base category')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['kb-categories.destroy', $category->id], 'id' => 'delete-form-' . $category->id]) !!}
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
