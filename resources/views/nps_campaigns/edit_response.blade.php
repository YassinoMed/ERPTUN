@extends('layouts.admin')
@section('page-title', __('Edit NPS Response'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('nps-campaigns.index') }}">{{ __('NPS Campaigns') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Response') }}</li>
@endsection
@section('content')
    <div class="card"><div class="card-body"><form method="POST" action="{{ route('nps-responses.update', $npsResponse) }}">@csrf @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-3"><label class="form-label">{{ __('Customer') }}</label><select name="customer_id" class="form-control"><option value="">{{ __('Anonymous / external') }}</option>@foreach($customers as $customerId => $customerName)<option value="{{ $customerId }}" @selected(old('customer_id', $npsResponse->customer_id) == $customerId)>{{ $customerName }}</option>@endforeach</select></div>
            <div class="col-md-4 mb-3"><label class="form-label">{{ __('Score') }}</label><input type="number" min="0" max="10" name="score" class="form-control" value="{{ old('score', $npsResponse->score) }}" required></div>
            <div class="col-md-4 mb-3"><label class="form-label">{{ __('Responded at') }}</label><input type="datetime-local" name="responded_at" class="form-control" value="{{ old('responded_at', $npsResponse->responded_at ? \Carbon\Carbon::parse($npsResponse->responded_at)->format('Y-m-d\\TH:i') : '') }}"></div>
            <div class="col-md-12 mb-3"><label class="form-label">{{ __('Feedback') }}</label><textarea name="feedback" class="form-control" rows="4">{{ old('feedback', $npsResponse->feedback) }}</textarea></div>
        </div>
        <div class="text-end"><a href="{{ route('nps-campaigns.show', $npsResponse->nps_campaign_id) }}" class="btn btn-light">{{ __('Cancel') }}</a><button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button></div>
    </form></div></div>
@endsection
