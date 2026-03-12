@extends('layouts.admin')

@section('page-title', __('Portfolio Management'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Portfolio Management') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Consolidate strategic initiatives, sponsors and delivery risk from a single portfolio workspace.') }}
@endsection

@section('action-btn')
    <div class="float-end">
        @can('create ppm portfolio')
            <a href="{{ route('ppm-portfolios.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus"></i></a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Portfolios') }}</span><strong class="ux-kpi-value">{{ $portfolios->count() }}</strong><span class="ux-kpi-meta">{{ __('active planning containers') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Initiatives') }}</span><strong class="ux-kpi-value">{{ $portfolios->sum('initiatives_count') }}</strong><span class="ux-kpi-meta">{{ __('linked transformation items') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Owners assigned') }}</span><strong class="ux-kpi-value">{{ $portfolios->whereNotNull('owner_id')->count() }}</strong><span class="ux-kpi-meta">{{ __('named sponsors') }}</span></div>
    </div>

    <div class="card ux-list-card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                    <tr>
                        <th>{{ __('Portfolio') }}</th>
                        <th>{{ __('Owner') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Window') }}</th>
                        <th>{{ __('Initiatives') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($portfolios as $portfolio)
                        <tr>
                            <td>
                                <div>{{ $portfolio->name }}</div>
                                <small class="text-muted">{{ $portfolio->priority ?: __('No priority set') }}</small>
                            </td>
                            <td>{{ optional($portfolio->owner)->name ?: '-' }}</td>
                            <td><span class="badge bg-info">{{ __(ucfirst(str_replace('_', ' ', $portfolio->status))) }}</span></td>
                            <td>{{ $portfolio->start_date ? Auth::user()->dateFormat($portfolio->start_date) : '-' }} / {{ $portfolio->end_date ? Auth::user()->dateFormat($portfolio->end_date) : '-' }}</td>
                            <td>{{ $portfolio->initiatives_count }}</td>
                            <td class="Action">
                                <div class="action-btn me-2"><a href="{{ route('ppm-portfolios.show', $portfolio) }}" class="mx-3 btn btn-sm bg-warning"><i class="ti ti-eye text-white"></i></a></div>
                                @can('edit ppm portfolio')
                                    <div class="action-btn me-2"><a href="{{ route('ppm-portfolios.edit', $portfolio) }}" class="mx-3 btn btn-sm bg-info"><i class="ti ti-pencil text-white"></i></a></div>
                                @endcan
                                @can('delete ppm portfolio')
                                    <div class="action-btn">
                                        <form method="POST" action="{{ route('ppm-portfolios.destroy', $portfolio) }}" id="delete-portfolio-{{ $portfolio->id }}">
                                            @csrf @method('DELETE')
                                            <a href="#" class="mx-3 btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-portfolio-{{ $portfolio->id }}').submit();"><i class="ti ti-trash text-white"></i></a>
                                        </form>
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
@endsection
