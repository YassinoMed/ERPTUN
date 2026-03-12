@extends('layouts.admin')

@section('page-title', __('Edit Portfolio'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppm-portfolios.index') }}">{{ __('Portfolio Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('ppm-portfolios.update', $ppmPortfolio) }}">
                @csrf @method('PUT')
                @include('ppm_portfolios._form')
                <div class="text-end">
                    <a href="{{ route('ppm-portfolios.show', $ppmPortfolio) }}" class="btn btn-light">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
