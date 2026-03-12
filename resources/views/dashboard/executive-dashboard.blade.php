@extends('layouts.admin')

@section('page-title')
    {{ __('Executive Overview') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Executive Overview') }}</li>
@endsection

@section('page-subtitle')
    {{ __('Track cross-functional signals, urgent approvals and the latest financial activity from one role-aware control room.') }}
@endsection

@section('content')
    <div class="ux-kpi-grid mb-4">
        @foreach ($summary as $item)
            <a href="{{ $item['route'] }}" class="ux-kpi-card executive-metric executive-metric-{{ $item['accent'] }}">
                <span class="ux-kpi-label">{{ $item['label'] }}</span>
                <strong class="ux-kpi-value">{{ $item['value'] }}</strong>
                <span class="ux-kpi-caption">{{ $item['headline'] }}</span>
                <small class="ux-kpi-meta">{{ $item['meta'] }}</small>
            </a>
        @endforeach
    </div>

    @if (!empty($operationalPulse))
        <div class="card executive-section mb-4">
            <div class="card-header">
                <h5 class="mb-1">{{ __('Operational Pulse') }}</h5>
                <p class="text-muted text-sm mb-0">{{ __('Cross-module operational signals from retail, medical, agro and industrial operations.') }}</p>
            </div>
            <div class="card-body">
                <div class="ux-kpi-grid">
                    @foreach ($operationalPulse as $item)
                        <a href="{{ $item['route'] }}" class="ux-kpi-card executive-metric executive-metric-{{ $item['accent'] }}">
                            <span class="ux-kpi-label">{{ $item['label'] }}</span>
                            <strong class="ux-kpi-value">{{ $item['value'] }}</strong>
                            <span class="ux-kpi-caption">{{ $item['headline'] }}</span>
                            <small class="ux-kpi-meta">{{ $item['meta'] }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xxl-4">
            <div class="card h-100 executive-section">
                <div class="card-header">
                    <h5 class="mb-1">{{ __('Business Snapshot') }}</h5>
                    <p class="text-muted text-sm mb-0">{{ __('The latest usage context and shortcuts for day-to-day steering.') }}</p>
                </div>
                <div class="card-body">
                    <div class="executive-quick-links">
                        @can('show crm dashboard')
                            <a href="{{ route('crm.dashboard') }}" class="executive-link-tile">
                                <i class="ti ti-target-arrow"></i>
                                <div>
                                    <strong>{{ __('Commercial cockpit') }}</strong>
                                    <span>{{ __('Watch lead flow, deals and contracts.') }}</span>
                                </div>
                            </a>
                        @endcan
                        @can('show account dashboard')
                            <a href="{{ route('dashboard') }}" class="executive-link-tile">
                                <i class="ti ti-report-money"></i>
                                <div>
                                    <strong>{{ __('Finance overview') }}</strong>
                                    <span>{{ __('Review invoices, bills and bank performance.') }}</span>
                                </div>
                            </a>
                        @endcan
                        @can('show project dashboard')
                            <a href="{{ route('project.dashboard') }}" class="executive-link-tile">
                                <i class="ti ti-briefcase"></i>
                                <div>
                                    <strong>{{ __('Delivery overview') }}</strong>
                                    <span>{{ __('Open projects, tasks and execution load.') }}</span>
                                </div>
                            </a>
                        @endcan
                        <a href="{{ route('approval-requests.index') }}" class="executive-link-tile">
                            <i class="ti ti-checkup-list"></i>
                            <div>
                                <strong>{{ __('Pending validations') }}</strong>
                                <span>{{ __('Process delayed approvals before they escalate.') }}</span>
                            </div>
                        </a>
                    </div>

                    @if ($savedViews->isNotEmpty())
                        <div class="mt-4">
                            <h6 class="mb-3">{{ __('Recent saved views') }}</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($savedViews as $savedView)
                                    <div class="executive-approval-row">
                                        <div>
                                            <strong>{{ $savedView->name }}</strong>
                                            <div class="text-muted text-sm">{{ ucfirst(str_replace('_', ' ', $savedView->module)) }}</div>
                                        </div>
                                        @if ($savedView->is_default)
                                            <span class="badge bg-primary">{{ __('Default') }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card h-100 executive-section">
                <div class="card-header">
                    <h5 class="mb-1">{{ __('Recent invoices') }}</h5>
                    <p class="text-muted text-sm mb-0">{{ __('Latest commercial cash events across your tenant.') }}</p>
                </div>
                <div class="card-body">
                    @forelse ($recentInvoices as $invoice)
                        <a href="{{ route('invoice.show', $invoice->id) }}" class="executive-approval-row executive-row-link">
                            <div>
                                <strong>{{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</strong>
                                <div class="text-muted text-sm">{{ optional($invoice->customer)->name ?: __('No customer') }}</div>
                            </div>
                            <div class="text-end">
                                <strong>{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</strong>
                                <div class="text-muted text-sm">{{ optional($invoice->created_at)->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="search-no-results">
                            <i class="ti ti-file-invoice"></i>
                            <p>{{ __('No recent invoices available.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="card h-100 executive-section">
                <div class="card-header">
                    <h5 class="mb-1">{{ __('Recent purchases') }}</h5>
                    <p class="text-muted text-sm mb-0">{{ __('Supplier spend and replenishment signals from the last entries.') }}</p>
                </div>
                <div class="card-body">
                    @forelse ($recentPurchases as $purchase)
                        <a href="{{ route('purchase.show', $purchase->id) }}" class="executive-approval-row executive-row-link">
                            <div>
                                <strong>{{ \Auth::user()->purchaseNumberFormat($purchase->purchase_id) }}</strong>
                                <div class="text-muted text-sm">{{ optional($purchase->vender)->name ?: __('No vendor') }}</div>
                            </div>
                            <div class="text-end">
                                <strong>{{ \Auth::user()->priceFormat($purchase->getTotal()) }}</strong>
                                <div class="text-muted text-sm">{{ optional($purchase->created_at)->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="search-no-results">
                            <i class="ti ti-shopping-cart"></i>
                            <p>{{ __('No recent purchases available.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card executive-section">
                <div class="card-header">
                    <h5 class="mb-1">{{ __('Urgent approvals') }}</h5>
                    <p class="text-muted text-sm mb-0">{{ __('Escalated or due approvals that require immediate attention.') }}</p>
                </div>
                <div class="card-body">
                    @forelse ($urgentApprovals as $approval)
                        <a href="{{ route('approval-requests.show', $approval->id) }}" class="executive-approval-row executive-row-link">
                            <div>
                                <strong>{{ optional($approval->approvalFlow)->name ?: __('Approval flow') }}</strong>
                                <div class="text-muted text-sm">
                                    {{ __('Step: :step', ['step' => optional($approval->currentStep)->name ?: __('Pending routing')]) }}
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $approval->status)) }}</span>
                            </div>
                            <div class="text-muted text-sm text-end">
                                @if ($approval->due_at)
                                    {{ __('Due :date', ['date' => $approval->due_at->format('Y-m-d H:i')]) }}
                                @else
                                    {{ __('No due date') }}
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="search-no-results">
                            <i class="ti ti-checkup-list"></i>
                            <p>{{ __('No urgent approvals right now.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
