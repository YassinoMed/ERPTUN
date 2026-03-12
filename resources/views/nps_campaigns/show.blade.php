@extends('layouts.admin')
@section('page-title', $npsCampaign->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('nps-campaigns.index') }}">{{ __('NPS Campaigns') }}</a></li>
    <li class="breadcrumb-item">{{ $npsCampaign->name }}</li>
@endsection
@section('content')
    @php
        $responses = $npsCampaign->responses;
        $promoters = $responses->where('sentiment', 'promoter')->count();
        $detractors = $responses->where('sentiment', 'detractor')->count();
        $score = $responses->count() ? round((($promoters - $detractors) / $responses->count()) * 100) : 0;
    @endphp
    <div class="ux-kpi-grid mb-4">
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('Responses') }}</span><strong class="ux-kpi-value">{{ $responses->count() }}</strong><span class="ux-kpi-meta">{{ __('captured answers') }}</span></div>
        <div class="ux-kpi-card"><span class="ux-kpi-label">{{ __('NPS score') }}</span><strong class="ux-kpi-value">{{ $score }}</strong><span class="ux-kpi-meta">{{ __('promoters minus detractors') }}</span></div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card"><div class="card-body"><h5>{{ __('Campaign Summary') }}</h5><p class="text-muted mb-2">{{ $npsCampaign->description ?: __('No description provided.') }}</p><div><strong>{{ __('Channel') }}:</strong> {{ strtoupper($npsCampaign->channel) }}</div><div><strong>{{ __('Status') }}:</strong> {{ __(ucfirst($npsCampaign->status)) }}</div></div></div>
            @can('create nps response')
                <div class="card"><div class="card-body"><h5>{{ __('Add Response') }}</h5><form method="POST" action="{{ route('nps-responses.store', $npsCampaign) }}">@csrf
                    <div class="mb-3"><label class="form-label">{{ __('Customer') }}</label><select name="customer_id" class="form-control"><option value="">{{ __('Anonymous / external') }}</option>@foreach($customers as $customerId => $customerName)<option value="{{ $customerId }}">{{ $customerName }}</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label">{{ __('Score') }}</label><input type="number" min="0" max="10" name="score" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">{{ __('Responded at') }}</label><input type="datetime-local" name="responded_at" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">{{ __('Feedback') }}</label><textarea name="feedback" class="form-control" rows="3"></textarea></div>
                    <button type="submit" class="btn btn-primary">{{ __('Add response') }}</button></form></div></div>
            @endcan
        </div>
        <div class="col-lg-8">
            <div class="card ux-list-card"><div class="card-body table-border-style"><div class="table-responsive"><table class="table"><thead><tr><th>{{ __('Customer') }}</th><th>{{ __('Score') }}</th><th>{{ __('Sentiment') }}</th><th>{{ __('Feedback') }}</th><th>{{ __('Action') }}</th></tr></thead><tbody>@forelse($responses as $response)<tr><td>{{ optional($response->customer)->name ?: __('Anonymous') }}</td><td>{{ $response->score }}/10</td><td><span class="badge bg-info">{{ __(ucfirst($response->sentiment)) }}</span></td><td>{{ $response->feedback ?: '-' }}</td><td class="Action">@can('edit nps response')<div class="action-btn me-2"><a href="{{ route('nps-responses.edit', $response) }}" class="mx-3 btn btn-sm bg-info"><i class="ti ti-pencil text-white"></i></a></div>@endcan @can('delete nps response')<div class="action-btn"><form method="POST" action="{{ route('nps-responses.destroy', $response) }}" id="delete-nps-response-{{ $response->id }}">@csrf @method('DELETE')<a href="#" class="mx-3 btn btn-sm bg-danger bs-pass-para" data-confirm="{{ __('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-nps-response-{{ $response->id }}').submit();"><i class="ti ti-trash text-white"></i></a></form></div>@endcan</td></tr>@empty<tr><td colspan="5" class="text-center text-muted">{{ __('No responses collected yet.') }}</td></tr>@endforelse</tbody></table></div></div></div>
        </div>
    </div>
@endsection
