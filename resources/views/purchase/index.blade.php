@extends('layouts.admin')
@section('page-title')
    {{__('Manage Purchase')}}
@endsection
@section('page-subtitle')
    {{ __('Follow supplier commitments, approval stages and procurement throughput in a single list.') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Purchase')}}</li>
@endsection
@push('script-page')
    <script>

        $('.copy_link').click(function (e) {
            e.preventDefault();
            var copyText = $(this).attr('href');

            document.addEventListener('copy', function (e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        });
    </script>
@endpush


@section('action-btn')
    <div class="float-end">


        @can('create purchase')
            <a href="{{ route('purchase.create',0) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection


@section('content')
    @php
        $pendingPurchases = $purchases->whereIn('status', [0, 1])->count();
        $rejectedPurchases = $purchases->where('status', 2)->count();
        $completedPurchases = $purchases->whereIn('status', [3, 4])->count();
        $vendorCount = $purchases->pluck('vender_id')->filter()->unique()->count();
    @endphp


    <div class="row">
        <div class="col-12 mb-4">
            <div class="ux-kpi-grid">
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Pending purchases') }}</span>
                    <strong class="ux-kpi-value">{{ $pendingPurchases }}</strong>
                    <span class="ux-kpi-meta">{{ __('draft or waiting approval') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Rejected purchases') }}</span>
                    <strong class="ux-kpi-value">{{ $rejectedPurchases }}</strong>
                    <span class="ux-kpi-meta">{{ __('blocked requests') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Completed flow') }}</span>
                    <strong class="ux-kpi-value">{{ $completedPurchases }}</strong>
                    <span class="ux-kpi-meta">{{ __('received or billed') }}</span>
                </div>
                <div class="ux-kpi-card">
                    <span class="ux-kpi-label">{{ __('Active vendors') }}</span>
                    <strong class="ux-kpi-value">{{ $vendorCount }}</strong>
                    <span class="ux-kpi-meta">{{ __('suppliers used in this list') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card ux-list-card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{__('Purchase')}}</th>
                                <th> {{__('Vendor')}}</th>
                                <th> {{__('Category')}}</th>
                                <th> {{__('Purchase Date')}}</th>
                                <th>{{__('Status')}}</th>
                                @if(Gate::check('edit purchase') || Gate::check('delete purchase') || Gate::check('show purchase'))
                                    <th > {{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>


                            @foreach ($purchases as $purchase)

                                <tr data-bulk-id="{{ $purchase->id }}">
                                    <td class="Id">
                                        <a href="{{ route('purchase.show',\Crypt::encrypt($purchase->id)) }}" class="btn btn-outline-primary">{{ Auth::user()->purchaseNumberFormat($purchase->purchase_id) }}</a>

                                    </td>

                                    <td> {{ (!empty( $purchase->vender)?$purchase->vender->name:'') }} </td>

                                    <td>{{ !empty($purchase->category)?$purchase->category->name:''}}</td>
                                    <td>{{ Auth::user()->dateFormat($purchase->purchase_date) }}</td>

                                    <td>
                                        @if($purchase->status == 0)
                                            <span class="purchase_status badge bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                        @elseif($purchase->status == 1)
                                            <span class="purchase_status badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                        @elseif($purchase->status == 2)
                                            <span class="purchase_status badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                        @elseif($purchase->status == 3)
                                            <span class="purchase_status badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                        @elseif($purchase->status == 4)
                                            <span class="purchase_status badge bg-primary p-2 px-3 rounded">{{ __(\App\Models\Purchase::$statues[$purchase->status]) }}</span>
                                        @endif
                                    </td>



                                    @if(Gate::check('edit purchase') || Gate::check('delete purchase') || Gate::check('show purchase'))
                                        <td class="Action">
                                            <span>

                                                @can('show purchase')
                                                    <div class="action-btn me-2">
                                                            <a href="{{ route('purchase.show',\Crypt::encrypt($purchase->id)) }}" class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="{{__('Show')}}" data-original-title="{{__('Detail')}}">
                                                                <i class="ti ti-eye text-white"></i>
                                                            </a>
                                                        </div>
                                                @endcan
                                                @can('edit purchase')
                                                    @if ($purchase->status != 3 && $purchase->status != 4)
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('purchase.edit',\Crypt::encrypt($purchase->id)) }}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="Edit" data-original-title="{{__('Edit')}}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endcan
                                                @can('delete purchase')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['purchase.destroy', $purchase->id],'class'=>'delete-form-btn','id'=>'delete-form-'.$purchase->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$purchase->id}}').submit();">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    @endif
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
