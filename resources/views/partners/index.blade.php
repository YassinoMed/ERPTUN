@extends('layouts.admin')

@section('page-title')
    {{ __('Partners') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Partners') }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('create partner')
            <a href="#" data-url="{{ route('partners.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Create Partner') }}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row"><div class="col-12"><div class="card"><div class="card-body table-border-style"><div class="table-responsive">
        <table class="table datatable">
            <thead><tr><th>{{ __('Partner') }}</th><th>{{ __('Type') }}</th><th>{{ __('Contact') }}</th><th>{{ __('Linked Account') }}</th><th>{{ __('Status') }}</th><th width="220px">{{ __('Action') }}</th></tr></thead>
            <tbody>
            @foreach ($partners as $partner)
                <tr>
                    <td><div>{{ $partner->name }}</div><small class="text-muted">{{ $partner->partner_code }}</small></td>
                    <td>{{ __(ucfirst($partner->partner_type)) }}</td>
                    <td>{{ $partner->contact_name ?: '-' }}<br><small class="text-muted">{{ $partner->email ?: $partner->phone ?: '-' }}</small></td>
                    <td>{{ optional($partner->customer)->name ?: optional($partner->vender)->name ?: '-' }}</td>
                    <td>{{ __(ucfirst($partner->status)) }}</td>
                    <td class="Action">
                        <div class="action-btn me-2"><a href="{{ route('partners.show', $partner) }}" class="mx-3 btn btn-sm align-items-center bg-warning"><i class="ti ti-eye text-white"></i></a></div>
                        @can('edit partner')
                        <div class="action-btn me-2"><a href="#" data-url="{{ route('partners.edit', $partner) }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Partner') }}" class="mx-3 btn btn-sm align-items-center bg-info"><i class="ti ti-pencil text-white"></i></a></div>
                        @endcan
                        @can('delete partner')
                        <div class="action-btn">{!! Form::open(['method' => 'DELETE', 'route' => ['partners.destroy', $partner], 'id' => 'delete-form-partner-' . $partner->id]) !!}<a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-partner-{{ $partner->id }}').submit();"><i class="ti ti-trash text-white"></i></a>{!! Form::close() !!}</div>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div></div></div></div>
@endsection
