@extends('layouts.admin')
@php
    $dir = asset(Storage::url('uploads/plan'));
@endphp
@section('page-title')
    {{ __('Manage Plan') }}
@endsection
@push('css-page')
    <style>
        .plans-shell {
            --plan-bg: linear-gradient(180deg, #f7f5ef 0%, #ffffff 68%);
            --plan-card-border: rgba(24, 36, 51, 0.08);
            --plan-muted: #617185;
            --plan-title: #132238;
            --plan-chip-bg: #f3efe3;
            --plan-chip-text: #3f4d61;
            --plan-enabled-bg: #e7f7ef;
            --plan-enabled-text: #157347;
            --plan-disabled-bg: #fff1f1;
            --plan-disabled-text: #b42318;
        }

        .plans-overview {
            background: var(--plan-bg);
            border: 1px solid rgba(24, 36, 51, 0.06);
            border-radius: 26px;
            padding: 1.4rem 1.6rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 22px 40px rgba(19, 34, 56, 0.06);
        }

        .plans-overview h2 {
            margin: 0;
            color: var(--plan-title);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .plans-overview p {
            margin: 0.45rem 0 0;
            color: var(--plan-muted);
            max-width: 720px;
        }

        .plan-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--plan-card-border);
            border-radius: 28px;
            background: #fff;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.11);
        }

        .plan-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 6px;
            background: linear-gradient(90deg, #0ea5e9 0%, #14b8a6 55%, #f59e0b 100%);
        }

        .plan-card-body {
            padding: 1.4rem;
        }

        .plan-topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .plan-label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.38rem 0.78rem;
            border-radius: 999px;
            background: #eef6ff;
            color: #0f5ea8;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .plan-status-stack {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .plan-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.38rem 0.72rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #f5f7fa;
            color: #425466;
        }

        .plan-pill.active {
            background: #e9f7ef;
            color: #157347;
        }

        .plan-pill.disabled {
            background: #fff4db;
            color: #9a6700;
        }

        .plan-price-wrap {
            margin-bottom: 1.25rem;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            gap: 0.45rem;
            color: var(--plan-title);
            line-height: 1;
        }

        .plan-price-value {
            font-size: clamp(2.2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.06em;
        }

        .plan-price-cycle {
            font-size: 0.95rem;
            color: var(--plan-muted);
            font-weight: 600;
        }

        .plan-subtext {
            color: var(--plan-muted);
            font-size: 0.92rem;
            margin-top: 0.35rem;
        }

        .plan-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1.2rem;
        }

        .plan-metric {
            border-radius: 20px;
            padding: 0.9rem 1rem;
            background: #f8fafc;
            border: 1px solid rgba(24, 36, 51, 0.06);
        }

        .plan-metric-value {
            display: block;
            color: var(--plan-title);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .plan-metric-label {
            display: block;
            color: var(--plan-muted);
            font-size: 0.8rem;
            margin-top: 0.15rem;
        }

        .plan-modules-card {
            border-radius: 24px;
            background: linear-gradient(180deg, #fcfcfb 0%, #f6f8fb 100%);
            border: 1px solid rgba(24, 36, 51, 0.06);
            padding: 1rem;
            margin-bottom: 1.15rem;
        }

        .plan-modules-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.8rem;
        }

        .plan-modules-title {
            color: var(--plan-title);
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0;
        }

        .plan-modules-count {
            color: var(--plan-muted);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .plan-progress {
            height: 8px;
            border-radius: 999px;
            background: #e8edf3;
            overflow: hidden;
            margin-bottom: 0.95rem;
        }

        .plan-progress-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #14b8a6 0%, #0ea5e9 100%);
        }

        .plan-modules-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.65rem;
        }

        .plan-module-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 16px;
            padding: 0.62rem 0.75rem;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .plan-module-chip.enabled {
            background: var(--plan-enabled-bg);
            color: var(--plan-enabled-text);
        }

        .plan-module-chip.disabled {
            background: var(--plan-disabled-bg);
            color: var(--plan-disabled-text);
        }

        .plan-module-chip i {
            font-size: 1rem;
        }

        .plan-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .plan-actions .btn {
            border-radius: 14px;
            padding: 0.7rem 1rem;
            font-weight: 600;
        }

        .plan-expiry {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed rgba(24, 36, 51, 0.12);
            color: var(--plan-muted);
            font-size: 0.88rem;
        }

        @media (max-width: 991px) {
            .plan-modules-grid,
            .plan-metrics {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Plan') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('create plan')
                    <a href="#" data-size="lg" data-url="{{ route('plans.create') }}" data-ajax-popup="true"
                        data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create New Plan') }}"
                        class="btn btn-sm btn-primary">
                        <i class="ti ti-plus"></i>
                    </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="plans-shell">
        <div class="plans-overview">
            <h2>{{ __('Subscription Plans') }}</h2>
            <p>{{ __('Pilotage SaaS plus clair: capacité, modules activés et actions commerciales dans une seule vue.') }}</p>
        </div>

        <div class="row">
        @foreach ($plans as $plan)
            @php
                $limits = [
                    ['label' => __('Users'), 'value' => $plan->max_users == -1 ? __('Unlimited') : $plan->max_users],
                    ['label' => __('Customers'), 'value' => $plan->max_customers == -1 ? __('Unlimited') : $plan->max_customers],
                    ['label' => __('Vendors'), 'value' => $plan->max_venders == -1 ? __('Unlimited') : $plan->max_venders],
                    ['label' => __('Clients'), 'value' => $plan->max_clients == -1 ? __('Unlimited') : $plan->max_clients],
                    ['label' => __('Storage'), 'value' => $plan->storage_limit == -1 ? __('Unlimited') : $plan->storage_limit . ' MB'],
                    ['label' => __('Trial'), 'value' => ($plan->trial_days ?: 0) . ' ' . __('days')],
                ];

                $modules = [
                    ['label' => __('Account'), 'enabled' => $plan->account == 1],
                    ['label' => __('CRM'), 'enabled' => $plan->crm == 1],
                    ['label' => __('HRM'), 'enabled' => $plan->hrm == 1],
                    ['label' => __('Project'), 'enabled' => $plan->project == 1],
                    ['label' => __('POS'), 'enabled' => $plan->pos == 1],
                    ['label' => __('Chat GPT'), 'enabled' => $plan->chatgpt == 1],
                    ['label' => __('Production'), 'enabled' => ($plan->production ?? 0) == 1],
                    ['label' => __('BTP Site Tracking'), 'enabled' => ($plan->btp_site_tracking ?? 0) == 1],
                    ['label' => __('BTP Subcontractors'), 'enabled' => ($plan->btp_subcontractors ?? 0) == 1],
                    ['label' => __('BTP Price Breakdown'), 'enabled' => ($plan->btp_price_breakdowns ?? 0) == 1],
                    ['label' => __('BTP Equipment Control'), 'enabled' => ($plan->btp_equipment_control ?? 0) == 1],
                    ['label' => __('Board Meetings'), 'enabled' => ($plan->board_meeting ?? 0) == 1],
                    ['label' => __('Cap Table'), 'enabled' => ($plan->cap_table ?? 0) == 1],
                    ['label' => __('Subsidiaries'), 'enabled' => ($plan->subsidiary ?? 0) == 1],
                    ['label' => __('Customer Recovery'), 'enabled' => ($plan->customer_recovery ?? 0) == 1],
                    ['label' => __('Visitors'), 'enabled' => ($plan->visitor ?? 0) == 1],
                    ['label' => __('Innovation Ideas'), 'enabled' => ($plan->innovation_idea ?? 0) == 1],
                    ['label' => __('Knowledge Base'), 'enabled' => ($plan->knowledge_base ?? 0) == 1],
                    ['label' => __('Document Repository'), 'enabled' => ($plan->document_repository ?? 0) == 1],
                    ['label' => __('Medical Services'), 'enabled' => ($plan->medical_service ?? 0) == 1],
                    ['label' => __('Medical Billing'), 'enabled' => ($plan->medical_invoice ?? 0) == 1],
                    ['label' => __('Pharmacy Stock'), 'enabled' => ($plan->pharmacy_medication ?? 0) == 1],
                    ['label' => __('Pharmacy Dispensing'), 'enabled' => ($plan->pharmacy_dispensation ?? 0) == 1],
                    ['label' => __('Hospital Rooms'), 'enabled' => ($plan->hospital_room ?? 0) == 1],
                    ['label' => __('Hospital Beds'), 'enabled' => ($plan->hospital_bed ?? 0) == 1],
                    ['label' => __('Hospital Admissions'), 'enabled' => ($plan->hospital_admission ?? 0) == 1],
                    ['label' => __('Delivery Notes'), 'enabled' => ($plan->delivery_note ?? 0) == 1],
                    ['label' => __('Agri Operations'), 'enabled' => ($plan->agri_operations ?? 0) == 1],
                    ['label' => __('Advanced Medical Ops'), 'enabled' => ($plan->medical_operations ?? 0) == 1],
                    ['label' => __('Retail Operations'), 'enabled' => ($plan->retail_operations ?? 0) == 1],
                ];

                $enabledModules = collect($modules)->where('enabled', true)->count();
                $totalModules = count($modules);
                $moduleCoverage = $totalModules > 0 ? round(($enabledModules / $totalModules) * 100) : 0;
                $durationLabel = __(\App\Models\Plan::$arrDuration[strtolower((string) $plan->duration)] ?? $plan->duration);
                $isActivePlan = \Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id;
                $isEnabledForSale = (int) $plan->is_disable === 1;
            @endphp
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card plan-card h-100">
                    <div class="plan-card-body">
                        <div class="plan-topbar">
                            <div>
                                <span class="plan-label">
                                    <i class="ti ti-rosette-discount-check"></i>
                                    {{ $plan->name }}
                                </span>
                            </div>
                            <div class="plan-status-stack">
                                @if ($isActivePlan)
                                    <span class="plan-pill active">
                                        <i class="ti ti-circle-check"></i>
                                        {{ __('Active') }}
                                    </span>
                                @endif
                                @if (\Auth::user()->type == 'super admin' && $plan->price > 0)
                                    <span class="plan-pill {{ $isEnabledForSale ? '' : 'disabled' }}">
                                        <i class="ti ti-bolt"></i>
                                        {{ $isEnabledForSale ? __('Enabled') : __('Disabled') }}
                                    </span>
                                    <div class="form-check form-switch custom-switch-v1 m-0">
                                        <input type="checkbox" name="plan_disable"
                                            class="form-check-input input-primary is_disable" value="1"
                                            data-id='{{ $plan->id }}' data-name="{{ __('plan') }}"
                                            {{ $isEnabledForSale ? 'checked' : '' }}>
                                        <label class="form-check-label" for="plan_disable"></label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="plan-price-wrap">
                            <div class="plan-price">
                                <span class="plan-price-value">{{ $admin_payment_setting['currency_symbol'] ?? '$' }}{{ number_format($plan->price) }}</span>
                                <span class="plan-price-cycle">/ {{ $durationLabel }}</span>
                            </div>
                            <div class="plan-subtext">
                                {{ __('Trial window') }}: {{ $plan->trial_days ?: 0 }} {{ __('days') }}
                            </div>
                        </div>

                        <div class="plan-metrics">
                            @foreach ($limits as $limit)
                                <div class="plan-metric">
                                    <span class="plan-metric-value">{{ $limit['value'] }}</span>
                                    <span class="plan-metric-label">{{ $limit['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="plan-modules-card">
                            <div class="plan-modules-head">
                                <p class="plan-modules-title">{{ __('Module Coverage') }}</p>
                                <span class="plan-modules-count">{{ $enabledModules }}/{{ $totalModules }} {{ __('enabled') }}</span>
                            </div>
                            <div class="plan-progress">
                                <div class="plan-progress-bar" style="width: {{ $moduleCoverage }}%;"></div>
                            </div>
                            <div class="plan-modules-grid">
                                @foreach ($modules as $module)
                                    <div class="plan-module-chip {{ $module['enabled'] ? 'enabled' : 'disabled' }}">
                                        <i class="ti {{ $module['enabled'] ? 'ti-circle-check' : 'ti-circle-x' }}"></i>
                                        <span>{{ $module['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if (\Auth::user()->type == 'super admin')
                            <div class="plan-actions">
                                <a title="{{ __('Edit') }}" href="#" class="btn btn-info"
                                    data-url="{{ route('plans.edit', $plan->id) }}" data-ajax-popup="true"
                                    data-title="{{ __('Edit Plan') }}" data-size="lg" data-bs-toggle="tooltip"
                                    data-bs-original-title="{{ __('Edit') }}">
                                    <i class="ti ti-pencil text-white"></i>
                                </a>
                                @if ($plan->id != 1)
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['plans.destroy', $plan->id],
                                        'id' => 'delete-form-' . $plan->id,
                                    ]) !!}
                                    <a href="#!" class="bs-pass-para btn btn-danger"
                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                    {!! Form::close() !!}
                                @endif
                            </div>
                        @endif

                        @if (\Auth::user()->type != 'super admin')
                            <div class="plan-actions">
                                @if ($plan->price > 0 && \Auth::user()->trial_plan == 0 && \Auth::user()->plan != $plan->id && $plan->trial == 1)
                                    <a href="{{ route('plan.trial', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                        class="btn btn-primary">{{ __('Start Free Trial') }}</a>
                                @endif
                                @if ($plan->id != \Auth::user()->plan && $plan->price > 0)
                                    <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                        class="btn btn-primary">{{ __('Buy Plan') }}</a>
                                @endif
                                @if ($plan->id != 1 && $plan->id != \Auth::user()->plan)
                                    @if (\Auth::user()->requested_plan != $plan->id)
                                        <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                            class="btn btn-outline-primary" data-title="{{ __('Send Request') }}"
                                            data-bs-toggle="tooltip" title="{{ __('Send Request') }}">
                                            <i class="ti ti-corner-up-right"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                            class="btn btn-danger" data-title="{{ __('Cancle Request') }}"
                                            data-bs-toggle="tooltip" title="{{ __('Cancle Request') }}">
                                            <i class="ti ti-x"></i>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        @endif

                        @if (\Auth::user()->type == 'company' && \Auth::user()->trial_expire_date)
                            @if (\Auth::user()->trial_plan == $plan->id)
                                <div class="plan-expiry">
                                    {{ __('Plan Trial Expired : ') }}
                                    {{ !empty(\Auth::user()->trial_expire_date) ? \Auth::user()->dateFormat(\Auth::user()->trial_expire_date) : 'lifetime' }}
                                </div>
                            @endif
                        @elseif (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                            <div class="plan-expiry">
                                {{ __('Plan Expired : ') }}
                                {{ !empty(\Auth::user()->plan_expire_date) ? \Auth::user()->dateFormat(\Auth::user()->plan_expire_date) : 'lifetime' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '#trial', function() {
            if ($(this).is(':checked')) {
                $('.plan_div').removeClass('d-none');
                $('#trial_days').attr("required", true);

            } else {
                $('.plan_div').addClass('d-none');
                $('#trial_days').removeAttr("required");
            }
        });
    </script>

    <script>
        $(document).on("click", ".is_disable", function() {

        var id = $(this).attr('data-id');
        var is_disable = ($(this).is(':checked')) ? $(this).val() : 0;

        $.ajax({
            url: '{{ route('plan.disable') }}',
            type: 'POST',
            data: {
                "is_disable": is_disable,
                "id": id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                if (data.success) {
                    show_toastr('success', data.success);
                } else {
                    show_toastr('error', data.error);

                }

            }
        });
    });
</script>
@endpush
