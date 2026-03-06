@extends('layouts.admin')

@section('page-title')
    {{ __('Upsell & Packages') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Upsell') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Service') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.upsell.services.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Price') }}</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Currency') }}</label>
                            <input type="text" name="currency" class="form-control" value="EUR">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Stock') }}</label>
                            <input type="number" name="stock" class="form-control" value="0">
                        </div>
                        <button class="btn btn-primary">{{ __('Save Service') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Package') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.upsell.packages.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Services') }}</label>
                            <select name="service_ids[]" class="form-control" multiple>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Package') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Generate Offer') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.upsell.offers.generate') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Reservation ID') }}</label>
                            <input type="number" name="reservation_id" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Customer ID') }}</label>
                            <input type="number" name="customer_id" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Generate') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Services') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Stock') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->price }} {{ $service->currency }}</td>
                                    <td>{{ $service->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Packages') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Items') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                                <tr>
                                    <td>{{ $package->name }}</td>
                                    <td>
                                        @foreach($package->items as $item)
                                            <span class="badge bg-light text-dark">{{ $item->service?->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Offers') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{ $offer->id }}</td>
                                    <td>{{ $offer->status }}</td>
                                    <td>
                                        <form action="{{ route('hotel.upsell.convert') }}" method="post" class="d-flex gap-2">
                                            @csrf
                                            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                            <select name="service_id" class="form-control">
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="quantity" class="form-control" value="1">
                                            <button class="btn btn-sm btn-primary">{{ __('Convert') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
