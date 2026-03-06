@extends('layouts.admin')

@section('page-title')
    {{__('Manage Cheques')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Cheques')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('import cheque')
            <a href="#" data-size="md" data-url="{{ route('cheques.import.form') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Import')}}" data-title="{{__('Import Cheques')}}" class="btn btn-sm btn-secondary me-2">
                <i class="ti ti-upload"></i>
            </a>
        @endcan
        @can('export cheque')
            <a href="{{ route('cheques.export') }}" class="btn btn-sm btn-secondary me-2" data-bs-toggle="tooltip" title="{{__('Export')}}">
                <i class="ti ti-download"></i>
            </a>
        @endcan
        @can('create cheque')
            <a href="#" data-size="lg" data-url="{{ route('cheques.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Cheque')}}" class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
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
                                <th>{{__('Cheque')}}</th>
                                <th>{{__('Bank')}}</th>
                                <th>{{__('Issue Date')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Status Date')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($cheques as $cheque)
                                <tr>
                                    <td>
                                        <div>{{ $cheque->cheque_number ?? '-' }}</div>
                                        <small class="text-muted">{{ $cheque->beneficiary_name }}</small>
                                    </td>
                                    <td>{{ $cheque->bank_name ?? '-' }}</td>
                                    <td>{{ \Auth::user()->dateFormat($cheque->issue_date) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($cheque->amount) }}</td>
                                    <td>{{ ucfirst($cheque->status) }}</td>
                                    <td>{{ $cheque->status_date ? \Auth::user()->dateFormat($cheque->status_date) : '-' }}</td>
                                    <td>
                                        <span>
                                            @can('print cheque')
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('cheques.print', $cheque->id) }}" class="mx-3 btn btn-sm align-items-center bg-secondary" target="_blank" data-bs-toggle="tooltip" title="{{__('Print')}}">
                                                        <i class="ti ti-printer text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('edit cheque')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('cheques.edit', $cheque->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Cheque')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete cheque')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['cheques.destroy', $cheque->id],'id'=>'delete-form-'.$cheque->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$cheque->id}}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </span>
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
