@extends('layouts.admin')

@section('page-title')
    {{ __('Medical Laboratory') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('medical.operations.index') }}">{{ __('Advanced Medical Operations') }}</a></li>
    <li class="breadcrumb-item">{{ __('Laboratory') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track panels, collection flow, validation throughput and critical patient samples from a dedicated laboratory board.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Ordered') }}</span><strong class="ux-kpi-value">{{ $kpis['ordered'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Collected') }}</span><strong class="ux-kpi-value">{{ $kpis['collected'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Validated') }}</span><strong class="ux-kpi-value">{{ $kpis['validated'] }}</strong></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Critical') }}</span><strong class="ux-kpi-value">{{ $kpis['critical'] }}</strong></div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h5>{{ __('Lab Order Workflow') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Panel') }}</th>
                        <th>{{ __('Ordered') }}</th>
                        <th>{{ __('Critical') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Update') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labOrders as $order)
                        <tr>
                            <td>{{ optional($order->patient)->first_name }} {{ optional($order->patient)->last_name }}</td>
                            <td>{{ $order->panel_name }}</td>
                            <td>{{ $order->ordered_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $order->critical_flag ? __('Yes') : __('No') }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>
                                <form action="{{ route('medical.operations.lab-orders.status', $order->id) }}" method="post" class="d-flex gap-2">
                                    @csrf
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="ordered" @selected($order->status === 'ordered')>{{ __('Ordered') }}</option>
                                        <option value="collected" @selected($order->status === 'collected')>{{ __('Collected') }}</option>
                                        <option value="validated" @selected($order->status === 'validated')>{{ __('Validated') }}</option>
                                        <option value="completed" @selected($order->status === 'completed')>{{ __('Completed') }}</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary">{{ __('Save') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>{{ __('Recent Lab Results') }}</h5></div>
        <div class="card-body table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Patient') }}</th>
                        <th>{{ __('Test') }}</th>
                        <th>{{ __('Result') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labResults as $result)
                        <tr>
                            <td>{{ optional($result->patient)->first_name }} {{ optional($result->patient)->last_name }}</td>
                            <td>{{ $result->test_name }}</td>
                            <td>{{ $result->result_value }}</td>
                            <td>{{ $result->result_date ? \Auth::user()->dateFormat($result->result_date) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
