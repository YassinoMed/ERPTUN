@extends('layouts.admin')

@section('page-title')
    {{ __('Cooperatives') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Cooperatives') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Cooperative') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.cooperatives.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Region') }}</label>
                            <input type="text" name="region" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Currency') }}</label>
                            <input type="text" name="currency" class="form-control" value="USD">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Cooperative') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Member') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.cooperatives.members.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Cooperative') }}</label>
                            <select name="cooperative_id" class="form-control" required>
                                @foreach($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}">{{ $cooperative->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Member Code') }}</label>
                            <input type="text" name="member_code" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Share %') }}</label>
                            <input type="number" step="0.01" name="share_percent" class="form-control" value="0">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Advance Balance') }}</label>
                            <input type="number" step="0.01" name="advance_balance" class="form-control" value="0">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="contact_phone" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Member') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Harvest Delivery') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.cooperatives.deliveries.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Cooperative') }}</label>
                            <select name="cooperative_id" class="form-control" required>
                                @foreach($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}">{{ $cooperative->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Member') }}</label>
                            <select name="member_id" class="form-control" required>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Lot ID') }}</label>
                            <input type="number" name="lot_id" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Crop Type') }}</label>
                            <input type="text" name="crop_type" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Quantity') }}</label>
                            <input type="number" step="0.001" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Unit') }}</label>
                            <input type="text" name="unit" class="form-control" value="kg">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Price / Unit') }}</label>
                            <input type="number" step="0.01" name="price_per_unit" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Delivery Date') }}</label>
                            <input type="date" name="delivery_date" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">{{ __('Record Delivery') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Revenue Distribution') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.cooperatives.distributions.create') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Cooperative') }}</label>
                            <select name="cooperative_id" class="form-control" required>
                                @foreach($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}">{{ $cooperative->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Period Start') }}</label>
                            <input type="date" name="period_start" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Period End') }}</label>
                            <input type="date" name="period_end" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Method') }}</label>
                            <select name="distribution_method" class="form-control">
                                <option value="quantity">{{ __('Quantity') }}</option>
                                <option value="share">{{ __('Share') }}</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Distribution') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Cooperatives') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Region') }}</th>
                                <th>{{ __('Currency') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cooperatives as $cooperative)
                                <tr>
                                    <td>{{ $cooperative->name }}</td>
                                    <td>{{ $cooperative->code }}</td>
                                    <td>{{ $cooperative->region }}</td>
                                    <td>{{ $cooperative->currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Members') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Share %') }}</th>
                                <th>{{ __('Advance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->member_code }}</td>
                                    <td>{{ $member->share_percent }}</td>
                                    <td>{{ $member->advance_balance }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Deliveries') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Member') }}</th>
                                <th>{{ __('Crop') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveries as $delivery)
                                <tr>
                                    <td>{{ $delivery->member_id }}</td>
                                    <td>{{ $delivery->crop_type }}</td>
                                    <td>{{ $delivery->quantity }} {{ $delivery->unit }}</td>
                                    <td>{{ $delivery->total_value }}</td>
                                    <td>{{ $delivery->delivery_date?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Distributions') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Method') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($distributions as $distribution)
                                <tr>
                                    <td>{{ $distribution->period_start?->format('Y-m-d') }} → {{ $distribution->period_end?->format('Y-m-d') }}</td>
                                    <td>{{ $distribution->total_revenue }}</td>
                                    <td>{{ $distribution->distribution_method }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Payouts') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Member') }}</th>
                                <th>{{ __('Gross') }}</th>
                                <th>{{ __('Advance') }}</th>
                                <th>{{ __('Net') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payouts as $payout)
                                <tr>
                                    <td>{{ $payout->member_id }}</td>
                                    <td>{{ $payout->gross_amount }}</td>
                                    <td>{{ $payout->advance_deducted }}</td>
                                    <td>{{ $payout->net_amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
