@extends('layouts.admin')

@section('page-title')
    {{ __('Purchase Contracts') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Purchase Contracts') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Contract') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.contracts.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Cooperative') }}</label>
                            <select name="cooperative_id" class="form-control">
                                <option value="">{{ __('Independent') }}</option>
                                @foreach($cooperatives as $cooperative)
                                    <option value="{{ $cooperative->id }}">{{ $cooperative->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Contract Number') }}</label>
                            <input type="text" name="contract_number" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Buyer Name') }}</label>
                            <input type="text" name="buyer_name" class="form-control" required>
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
                            <label class="form-label">{{ __('Fixed Price') }}</label>
                            <input type="number" step="0.01" name="fixed_price" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Currency') }}</label>
                            <input type="text" name="price_currency" class="form-control" value="USD">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Delivery Start') }}</label>
                            <input type="date" name="delivery_start" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Delivery End') }}</label>
                            <input type="date" name="delivery_end" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Hedge Ratio %') }}</label>
                            <input type="number" step="0.01" name="hedge_ratio" class="form-control" value="0">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Contract') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Hedge Position') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.contracts.hedges.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Contract') }}</label>
                            <select name="contract_id" class="form-control" required>
                                @foreach($contracts as $contract)
                                    <option value="{{ $contract->id }}">{{ $contract->contract_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Instrument') }}</label>
                            <input type="text" name="instrument" class="form-control" value="FUT">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Position Type') }}</label>
                            <select name="position_type" class="form-control">
                                <option value="future">{{ __('Future') }}</option>
                                <option value="option">{{ __('Option') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Quantity') }}</label>
                            <input type="number" step="0.001" name="quantity" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Price') }}</label>
                            <input type="number" step="0.01" name="price" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Opened At') }}</label>
                            <input type="date" name="opened_at" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Create Hedge') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Price Index') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('agri.contracts.price-indices.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Crop Type') }}</label>
                            <input type="text" name="crop_type" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Market') }}</label>
                            <input type="text" name="market" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Price') }}</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Currency') }}</label>
                            <input type="text" name="currency" class="form-control" value="USD">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Recorded At') }}</label>
                            <input type="datetime-local" name="recorded_at" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Index') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Contracts') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Contract') }}</th>
                                <th>{{ __('Buyer') }}</th>
                                <th>{{ __('Crop') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Delivery') }}</th>
                                <th>{{ __('Hedge %') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td>{{ $contract->contract_number }}</td>
                                    <td>{{ $contract->buyer_name }}</td>
                                    <td>{{ $contract->crop_type }}</td>
                                    <td>{{ $contract->quantity }} {{ $contract->unit }}</td>
                                    <td>{{ $contract->fixed_price }} {{ $contract->price_currency }}</td>
                                    <td>{{ $contract->delivery_start?->format('Y-m-d') }} → {{ $contract->delivery_end?->format('Y-m-d') }}</td>
                                    <td>{{ $contract->hedge_ratio }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Hedge Positions') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Contract') }}</th>
                                <th>{{ __('Instrument') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hedgePositions as $hedge)
                                <tr>
                                    <td>{{ $hedge->contract_id }}</td>
                                    <td>{{ $hedge->instrument }}</td>
                                    <td>{{ $hedge->position_type }}</td>
                                    <td>{{ $hedge->quantity }}</td>
                                    <td>{{ $hedge->price }}</td>
                                    <td>{{ $hedge->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Price Indices') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Crop') }}</th>
                                <th>{{ __('Market') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Recorded At') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceIndices as $index)
                                <tr>
                                    <td>{{ $index->crop_type }}</td>
                                    <td>{{ $index->market }}</td>
                                    <td>{{ $index->price }} {{ $index->currency }}</td>
                                    <td>{{ $index->recorded_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
