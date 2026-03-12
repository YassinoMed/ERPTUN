@extends('layouts.admin')
@section('page-title', __('Edit NPS Campaign'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('nps-campaigns.index') }}">{{ __('NPS Campaigns') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('nps-campaigns.update', $npsCampaign) }}">@csrf @method('PUT') @include('nps_campaigns._form')<div class="text-end"><a href="{{ route('nps-campaigns.show', $npsCampaign) }}" class="btn btn-light">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button></div></form></div></div>
@endsection
