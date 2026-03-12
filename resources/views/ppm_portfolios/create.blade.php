@extends('layouts.admin')

@section('page-title', __('Create Portfolio'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppm-portfolios.index') }}">{{ __('Portfolio Management') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('ppm-portfolios.store') }}">
                @csrf
                @include('ppm_portfolios._form')
                <div class="text-end">
                    <a href="{{ route('ppm-portfolios.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
